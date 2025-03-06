-- Enable UUID extension if not already enabled
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Drop existing tables if they exist (be careful with this in production)
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS resources CASCADE;
DROP TABLE IF EXISTS validations CASCADE;
DROP TABLE IF EXISTS participants CASCADE;
DROP TABLE IF EXISTS profiles CASCADE;

-- Create tables
CREATE TABLE IF NOT EXISTS profiles (
    id uuid REFERENCES auth.users ON DELETE CASCADE PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'participant' CHECK (role IN ('admin', 'validator', 'participant')),
    avatar_url TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW()),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW())
);

CREATE TABLE IF NOT EXISTS participants (
    id uuid DEFAULT uuid_generate_v4() PRIMARY KEY,
    profile_id uuid REFERENCES profiles(id) ON DELETE CASCADE,
    registration_number VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(20),
    institution VARCHAR(255),
    qr_code VARCHAR(255) UNIQUE,
    status VARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'validated', 'rejected')),
    validation_count INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW()),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW())
);

CREATE TABLE IF NOT EXISTS validations (
    id uuid DEFAULT uuid_generate_v4() PRIMARY KEY,
    participant_id uuid REFERENCES participants(id) ON DELETE CASCADE,
    validator_id uuid REFERENCES profiles(id),
    type VARCHAR(50) NOT NULL CHECK (type IN ('registration', 'attendance', 'certificate')),
    status VARCHAR(50) NOT NULL DEFAULT 'approved' CHECK (status IN ('approved', 'rejected')),
    notes TEXT,
    validated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW())
);

CREATE TABLE IF NOT EXISTS resources (
    id uuid DEFAULT uuid_generate_v4() PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_url TEXT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size BIGINT,
    downloads INT DEFAULT 0,
    is_public BOOLEAN DEFAULT true,
    uploaded_by uuid REFERENCES profiles(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW()),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW())
);

CREATE TABLE IF NOT EXISTS notifications (
    id uuid DEFAULT uuid_generate_v4() PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info' CHECK (type IN ('info', 'success', 'warning', 'error')),
    is_read BOOLEAN DEFAULT false,
    recipient_id uuid REFERENCES profiles(id),
    created_by uuid REFERENCES profiles(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc', NOW())
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_participants_profile_id ON participants(profile_id);
CREATE INDEX IF NOT EXISTS idx_participants_status ON participants(status);
CREATE INDEX IF NOT EXISTS idx_validations_participant_id ON validations(participant_id);
CREATE INDEX IF NOT EXISTS idx_validations_validator_id ON validations(validator_id);
CREATE INDEX IF NOT EXISTS idx_validations_type ON validations(type);
CREATE INDEX IF NOT EXISTS idx_resources_uploaded_by ON resources(uploaded_by);
CREATE INDEX IF NOT EXISTS idx_resources_is_public ON resources(is_public);
CREATE INDEX IF NOT EXISTS idx_notifications_recipient_id ON notifications(recipient_id);
CREATE INDEX IF NOT EXISTS idx_notifications_created_by ON notifications(created_by);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications(is_read);

-- Enable Row Level Security on all tables
ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE participants ENABLE ROW LEVEL SECURITY;
ALTER TABLE validations ENABLE ROW LEVEL SECURITY;
ALTER TABLE resources ENABLE ROW LEVEL SECURITY;
ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;

-- Create policies for profiles table
CREATE POLICY "Public profiles are viewable by everyone" ON profiles
    FOR SELECT USING (true);

CREATE POLICY "Users can update own profile" ON profiles
    FOR UPDATE USING (auth.uid() = id);

CREATE POLICY "Admins can manage all profiles" ON profiles
    FOR ALL USING (
        EXISTS (
            SELECT 1 FROM profiles WHERE id = auth.uid() AND role = 'admin'
        )
    );

-- Create policies for participants table
CREATE POLICY "Participants can view their own data" ON participants
    FOR SELECT USING (profile_id = auth.uid());

CREATE POLICY "Validators can view all participants" ON participants
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM profiles WHERE id = auth.uid() AND role IN ('admin', 'validator')
        )
    );

CREATE POLICY "Admins can manage all participants" ON participants
    FOR ALL USING (
        EXISTS (
            SELECT 1 FROM profiles WHERE id = auth.uid() AND role = 'admin'
        )
    );

-- Create policies for validations table
CREATE POLICY "Participants can view their validations" ON validations
    FOR SELECT USING (
        participant_id IN (
            SELECT id FROM participants WHERE profile_id = auth.uid()
        )
    );

CREATE POLICY "Validators can manage validations" ON validations
    FOR ALL USING (
        EXISTS (
            SELECT 1 FROM profiles WHERE id = auth.uid() AND role IN ('admin', 'validator')
        )
    );

-- Create policies for resources table
CREATE POLICY "Anyone can view public resources" ON resources
    FOR SELECT USING (is_public = true);

CREATE POLICY "Users can view their uploaded resources" ON resources
    FOR SELECT USING (uploaded_by = auth.uid());

CREATE POLICY "Admins can manage all resources" ON resources
    FOR ALL USING (
        EXISTS (
            SELECT 1 FROM profiles WHERE id = auth.uid() AND role = 'admin'
        )
    );

-- Create policies for notifications table
CREATE POLICY "Users can view their notifications" ON notifications
    FOR SELECT USING (recipient_id = auth.uid());

CREATE POLICY "Users can mark their notifications as read" ON notifications
    FOR UPDATE USING (recipient_id = auth.uid());

CREATE POLICY "Admins can manage all notifications" ON notifications
    FOR ALL USING (
        EXISTS (
            SELECT 1 FROM profiles WHERE id = auth.uid() AND role = 'admin'
        )
    );

-- Create functions
CREATE OR REPLACE FUNCTION handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO public.profiles (id, email, name, role)
    VALUES (
        new.id,
        new.email,
        COALESCE(new.raw_user_meta_data->>'name', split_part(new.email, '@', 1)),
        COALESCE(new.raw_user_meta_data->>'role', 'participant')
    );
    RETURN new;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION update_validation_count()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' AND NEW.status = 'approved' THEN
        UPDATE participants
        SET validation_count = validation_count + 1
        WHERE id = NEW.participant_id;
        
        -- Auto-update participant status if they have all validations
        UPDATE participants
        SET status = 'validated'
        WHERE id = NEW.participant_id
        AND validation_count >= 3;  -- Assuming 3 validations are required
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Create triggers
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
    AFTER INSERT ON auth.users
    FOR EACH ROW EXECUTE PROCEDURE handle_new_user();

DROP TRIGGER IF EXISTS on_validation_added ON validations;
CREATE TRIGGER on_validation_added
    AFTER INSERT ON validations
    FOR EACH ROW EXECUTE PROCEDURE update_validation_count();

-- Create initial admin user (you'll need to sign up with this email first)
INSERT INTO auth.users (email, role)
VALUES ('admin@napps.com', 'admin')
ON CONFLICT (email) DO NOTHING;
