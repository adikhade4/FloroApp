<?php
namespace App\Services;
use App\Repositories\AuthenticationLogRepository;
use App\User;
use Illuminate\Http\Request;
class AuthenticationService
{
    /**
     * @var AuthenticationLogRepository $authenticationLog
     */
    private $authenticationLogRepository;
    /**
     * AuthenticationService constructor.
     * Initialize object/instances of the classes.
     *
     * @param AuthenticationLogRepository $authenticationLogRepository
     */
    public function __construct(AuthenticationLogRepository $authenticationLogRepository)
    {
        $this->authenticationLogRepository = $authenticationLogRepository;
    }
    
    public function storeLoginActivityOfUser(Request $request, User $user)
    {
        $logDetails = [
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
        ];
        $this->authenticationLogRepository->create($logDetails);
    }
}