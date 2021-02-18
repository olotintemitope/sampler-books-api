<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\BookRepository;
use App\Http\Repository\UserRepository;
use App\Http\Traits\ValidationTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    use ValidationTrait;

    private const CHECKED_OUT = 'CHECKED_OUT';
    private const AVAILABLE = 'AVAILABLE';

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var BookRepository
     */
    private $bookRepository;

    public function __construct(UserRepository $userRepository, BookRepository $bookRepository)
    {
        $this->userRepository = $userRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * This method gets all the users
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->sendResponse(
            $this->userRepository->getAll()
                ->toArray()
        );
    }

    /**
     * Create a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = $this->getCreateUserValidator($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);

        try {
            $user = $this->userRepository->create($data);
            $userToken = $user->createToken('Sampler')->accessToken;
            $user->token = $userToken;

            return $this->sendResponse($user->toArray(), Response::HTTP_CREATED);
        } catch (Exception $exception) {
           return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Update user details
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $this->userRepository->findOne($id);

        if (null === $user) {
            return $this->sendError('User not found');
        }

        $validator = $this->getUpdateUserValidator($request);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $this->userRepository->update($id, $request->all());

        return $this->sendResponse([], Response::HTTP_OK);
    }

    /**
     * Delete user details
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $user = $this->userRepository->findOne($id);

        if (null === $user) {
            return $this->sendError('User not found');
        }

        $this->userRepository->delete($id);

        return $this->sendResponse([], Response::HTTP_OK);
    }

    /**
     * Find a single user details
     *
     * @param $id
     * @return JsonResponse
     */
    public function find($id): JsonResponse
    {
        $user = $this->userRepository->findOne($id);

        if (null === $user) {
            return $this->sendError('User not found');
        }

        return $this->sendResponse($user->toArray(), Response::HTTP_OK);
    }

    /**
     * Checkin books
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function checkInBooks(Request $request, int $id): JsonResponse
    {
        $validator = $this->getBooksValidator($request);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = $this->userRepository->findOne($id);
        if (null === $user) {
            return $this->sendError('User not found');
        }

        $booksError = $this->getBooksById($request);
        $errors = $this->checkAvailableBooks($request);
        $validator->errors()->merge(array_merge($booksError, $errors));

        if (count($validator->errors()->messages()) > 0) {
            $validator->errors()->add('books', $validator->errors()->messages());

            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Update the Book Availability to CHECKED_OUT
        $user->books()->attach($request->books, [
            'action' => 'CHECKIN'
        ]);

        $this->updateBooks($request, self::CHECKED_OUT);

        return $this->sendResponse([], Response::HTTP_CREATED);
    }

    /**
     * Checkout books
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function checkOutBooks(Request $request, int $id): JsonResponse
    {
        $validator = $this->getBooksValidator($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = $this->userRepository->findOne($id);
        if (null === $user) {
            return $this->sendError('User not found');
        }

        $this->getValidator($request, $validator);

        if (count($validator->errors()->messages()) > 0) {
            $validator->errors()->add('books', $validator->errors()->messages());

            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Update the Book Availability to AVAILABLE
        $user->books()->attach($request->books, [
            'action' => 'CHECKOUT'
        ]);

        $this->updateBooks($request, self::AVAILABLE);

        return $this->sendResponse([], Response::HTTP_CREATED);
    }

    /**
     * Validate create user
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getCreateUserValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => $this->getPasswordValidation(),
            'date_of_birth' => 'required|date|date_format:Y-m-d'
        ]);
    }

    /**
     * Validate update user details
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getUpdateUserValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'sometimes|unique:users|max:255',
            'email' => 'sometimes|email|unique:users|max:255',
            'password' => $this->getOptionalPasswordValidation(),
            'date_of_birth' => 'sometimes|date|date_format:Y-m-d'
        ]);
    }

    /**
     * Check if books exists
     *
     * @param Request $request
     * @return array
     */
    protected function getBooksById(Request $request): array
    {
        $errors = [];
        collect($request->books)->each(function ($bookId) use (&$errors) {
            $book = $this->bookRepository->findOne($bookId);
            if (null === $book) {
                $errors[] = "Book with ID: {$bookId} not found";
            }
        });
        return $errors;
    }

    /**
     * Check if books exists
     *
     * @param Request $request
     * @return array
     */
    protected function checkAvailableBooks(Request $request): array
    {
        $errors = [];

        $this->bookRepository
            ->query()
            ->whereIn('id', $request->books)
            ->get()
            ->each(function ($book) {
                if (null !== $book && $book->status === self::CHECKED_OUT) {
                    $errors[] = "Book with ID: {$book->id} not available";
                }
            });

        return $errors;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getBooksValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'books' => 'required|array|min:1',
            'books.*' => 'required|distinct|min:1'
        ]);
    }

    /**
     * Update the book status
     *
     * @param Request $request
     * @param string $status
     */
    protected function updateBooks(Request $request, string $status): void
    {
        collect($request->books)->each(function ($bookId) use ($status) {
            $this->bookRepository->update($bookId, [
                'status' => $status
            ]);
        });
    }

    /**
     * Check and merge validation errors
     *
     * @param Request $request
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function getValidator(Request $request, \Illuminate\Contracts\Validation\Validator $validator): void
    {
        $booksError = $this->getBooksById($request);
        $errors = $this->checkAvailableBooks($request);
        $validator->errors()->merge(array_merge($booksError, $errors));
    }
}
