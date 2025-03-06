use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ValidationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ValidatorController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\MealController;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    // Participant Routes
    Route::get('/participant-profile', [ParticipantController::class, 'show']);
    Route::post('/participant-profile', [ParticipantController::class, 'update']);
    Route::get('/qr-code', [ParticipantController::class, 'getQrCode']);

    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::post('/verify', [PaymentController::class, 'verify']);
        Route::get('/receipt', [PaymentController::class, 'generateReceipt']);
    });

    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    // Resource Routes
    Route::prefix('resources')->group(function () {
        Route::get('/', [ResourceController::class, 'index']);
        Route::get('/{resource}', [ResourceController::class, 'show']);
    });

    // Meal Routes
    Route::prefix('meals')->group(function () {
        Route::get('/today', [MealController::class, 'today']);
        Route::get('/history', [MealController::class, 'history']);
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/export-participants', [AdminController::class, 'exportParticipants']);
        Route::put('/participants/{id}', [AdminController::class, 'updateParticipant']);
        Route::post('/validators', [AdminController::class, 'createValidator']);
        Route::post('/resources', [ResourceController::class, 'store']);
        Route::put('/resources/{resource}', [ResourceController::class, 'update']);
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
        Route::post('/notifications', [NotificationController::class, 'store']);
    });

    // Validator Routes
    Route::middleware('role:validator')->prefix('validator')->group(function () {
        Route::post('/validate', [ValidatorController::class, 'validate']);
        Route::get('/recent-validations', [ValidatorController::class, 'recentValidations']);
        Route::post('/meals/validate', [MealController::class, 'validate']);
        Route::get('/meals/stats', [MealController::class, 'stats']);
    });
});
