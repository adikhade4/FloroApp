<?php
namespace App\Observers;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\User;
use Illuminate\Support\Facades\Auth;
class UserObserver
{
    
    private $userService;
    
    private $userRepository;
  
    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }
   
    public function updating(User $user)
    {
        $userBeforeUpdated = $this->userRepository->find($user->id);
        
        $this->userService->trackUserActivity(Auth::id(),User::class, config('constants.TRACK_USER_FIELDS'),
            $userBeforeUpdated, $user);
    }
}