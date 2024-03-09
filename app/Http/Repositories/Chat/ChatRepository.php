<?php

namespace App\Http\Repositories\Chat;

use App\Events\MessageSent;
use App\Http\Repositories\Chat\ChatInterface;
use App\Http\Repositories\User\UserInterface;
use Musonza\Chat\Chat;

class ChatRepository implements ChatInterface
{
    public $loggedinUser;

    public function __construct(public Chat $model, public UserInterface $UserI)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function conversations($request)
    {
        if (!$this->loggedinUser) {
            return ['status' => false, 'errors' => [trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]];
        }

        $conversations = $this->model->conversations()->setParticipant($this->loggedinUser)->limit($request->per_page ?: 20)->page($request->page ?: 1)->get();

        return ['status' => true, 'data' => $conversations];
    }

    public function conversation($request, $username)
    {
        $participant = $this->UserI->findByUsername($username);
        if (!$participant || !$this->loggedinUser) {
            return ['status' => false, 'errors' => [trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]];
        }

        $conversation = $this->model->conversations()->between($this->loggedinUser, $participant);

        if (!$conversation) {
            return ['status' => true, 'data' => []];
        }
        //mark as read
        $this->model->conversation($conversation)->setParticipant($this->loggedinUser)->readAll();

        $messages = $this->model->conversation($conversation)->setParticipant($this->loggedinUser)->setPaginationParams([
                'page' => $request->page ?: 1,
                'perPage' => $request->per_page ?: 20,
                'sorting' => "desc",
            ])->getMessages();

        return ['status' => true, 'data' => $messages];
    }

    public function createConversation($request, $username)
    {
        $participant = $this->UserI->findByUsername($username);
        if (!$participant || !$this->loggedinUser) {
            return ['status' => false, 'errors' => [trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]];
        }

        if (!$participant->is_messagable) {
            return ['status' => false, 'errors' => [trans('error.cantMessage', ['model' => trans_class_basename($this->UserI->model)])]];
        }

        $participants = [$this->loggedinUser, $participant];

        try {
            $conversation = $this->model->makeDirect()->createConversation($participants);
        } catch (\Throwable $th) {
            $conversation = $this->model->conversations()->between($participants[0], $participants[1]);
        }

        $message = $this->model->message($request->message)
            ->from($participants[0])
            ->to($conversation)
            ->send();

        MessageSent::dispatch($message);

        return ['status' => true, 'data' => []];
    }
}
