<?php
namespace App\Services;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\UserCreatedEmail;
use App\Repositories\UserActivityRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Okipa\LaravelBootstrapTableList\TableList;
use Carbon\Carbon;
use App\Services\UserService;
use Validator;


class UserService
{
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;
    /**
     * @var UserActivityRepository $userActivityRepository
     */
    private $userActivityRepository;
    /**
     * Class level constants to avoid SONAR LINT errors.
     */
    const ACTIVE = 1;
    const IN_ACTIVE = 0;
    /**
     * UserService constructor.
     * Initialize object/instance for classes.
     *
     * @param UserRepository $userRepository
     * @param UserActivityRepository $userActivityRepository
     */
    public function __construct(UserRepository $userRepository, UserActivityRepository $userActivityRepository)
    {
        $this->userRepository = $userRepository;
        $this->userActivityRepository = $userActivityRepository;
    }
    /**
     * Method to create the user.
     *
     * @param CreateUserRequest $request
     * @return Collection|null
     */
    public function createUser(CreateUserRequest $request)
    {
        $inputData = $request->all();
        unset($inputData['password_confirmation']);
        $password = $inputData['password'];
        $inputData['password'] = Hash::make($inputData['password']);
        $user = $this->userRepository->create($inputData);
        if (!$user) {
            return null;
        }
        
        ($inputData['email']);
        return $user;
    }
    
    public function getAllUsers() : TableList
    {
        $table = $this->userRepository->getUsersList();
        $table->addColumn('username')
            ->setTitle('User Name')
            ->isSortable()
            ->isSearchable()
            ->useForDestroyConfirmation();
        $table->addColumn('first_name')
            ->setTitle('First Name')
            ->isSortable();
        $table->addColumn('last_name')
            ->setTitle('Last Name')
            ->isSortable();
        $table->addColumn('email')
            ->setTitle('Email')
            ->isSearchable()
            ->isSortable();
        $table->addColumn('created_at')
            ->setTitle('Created At')
            ->isSortable()
            ->sortByDefault('desc')
            ->setColumnDateTimeFormat('d-M-Y');
        $table->addColumn('last_login_at')
            ->setTitle('Last Login At')
            ->isSortable()
            ->setColumnDateTimeFormat('d-M-Y H:i');
       
        return $table;
    }
    /**
     * Method to find the user by user ID.
     *
     * @param $id
     * @return Collection
     */
    public function getUser(string $id)
    {
        return $this->userRepository->find($id);
    }
    
    public function deleteUser(string $id) : bool
    {
        if ($id == Auth::id()) {
            return false;
        }
        $this->userRepository->update($id, ['is_active' => self::IN_ACTIVE]);
        return  $this->userRepository->delete($id);
    }
    
    public function updateUser($request, string $userId)
    {
        $inputData = $request->all();
        if (isset($inputData['password']) && !empty($inputData['password'])) {
            $inputData['password'] = Hash::make($inputData['password']);
        } else {
            unset($inputData['password']);
        }
        
        unset($inputData['password_confirmation']);
        if (!isset($inputData['is_active'])) {
            $inputData['is_active'] = self::IN_ACTIVE;
        }
        return $this->userRepository->update($userId, $inputData);
    }
    
    public function trackUserActivity($modifiedBy, $class, $trackableFields, $dataBeforeUpdated, $dataAfterUpdated) : bool
    {
        $dataAfterUpdated = $dataAfterUpdated->toArray();
        $dataBeforeUpdated = $dataBeforeUpdated->toArray();
        $updatedData = array_diff($dataAfterUpdated, $dataBeforeUpdated);
        if (empty($updatedData)) {
            return true;
        }
        $trackableData = array_only($updatedData, $trackableFields);
        if (empty($trackableData)) {
            return true;
        }
        $trackableDataToInsert = [];
        foreach ($trackableFields as $field) {
        if (isset($trackableData[$field]) && !empty($trackableData[$field])) {
        $information['entity_type'] = $class;
        $information['entity_id'] = $dataBeforeUpdated['id'];
        $information['field_name'] = $field;
        $information['old_value'] = $dataBeforeUpdated[$field];
        $information['new_value'] = $dataAfterUpdated[$field];
        $information['modified_by'] = $modifiedBy;
        $information['created_at'] = Carbon::now()->toDateTimeString();
        $information['updated_at'] = Carbon::now()->toDateTimeString();
        
        $trackableDataToInsert[] = $information;
        }
        }
        
        $this->userActivityRepository->insertMultipleRows($trackableDataToInsert);  
        return true;
    }
  
}