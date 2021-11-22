<?php


namespace App\Services\Bot\Sns;


use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use App\Repositories\SnsUidMap\SnsUidMapRepositoryInterface;

class SnsUidMapService
{
    /**
     * @var ResponseInfoRepositoryInterface
     */
    private $response_info_repository;
    /**
     * @var SnsUidMapRepositoryInterface
     */
    private $sns_uid_map_repository;

    /**
     * @var integer
     */
    private $channel;

    /**
     * SnsUidMapService constructor.
     * @param SnsUidMapRepositoryInterface $sns_uid_map_repository
     * @param ResponseInfoRepositoryInterface $response_info_repository
     */
    public function __construct(SnsUidMapRepositoryInterface $sns_uid_map_repository, ResponseInfoRepositoryInterface $response_info_repository)
    {
        $this->sns_uid_map_repository = $sns_uid_map_repository;
        $this->response_info_repository = $response_info_repository;
        $this->channel = config('const.bot.channel.web.id');
    }

    /**
     * @param $uid
     * @return mixed|string
     */
    public function getChatId($uid)
    {
        $hash = $this->sns_uid_map_repository->getOneById($uid);
        if (is_null($hash)) {
            $chat_id = $this->createMap($uid)['chat_id'];
        } else {
            $chat_id = $hash['chat_id'];
        }
        return $chat_id;
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function generateEnqueteKey($uid)
    {
        $hash = $this->sns_uid_map_repository->getOneById($uid);
        if (is_null($hash)) {
            $hash = $this->createMap($uid);
        } else {
            $hash['enquete_key'] = $this->sns_uid_map_repository->getUniqueEnqueteKey();
            $this->sns_uid_map_repository->update($uid, $hash);
        }
        return $hash['enquete_key'];
    }

    /**
     * @param $enquete_key
     * @return mixed
     */
    public function findEnqueteKey($enquete_key)
    {
        return $this->sns_uid_map_repository->findOneBy(['enquete_key' => $enquete_key]);
    }

    /**
     * @param $uid
     * @param $chat_id
     * @return mixed
     */
    public function updateChatId($uid, $chat_id)
    {
        return $this->sns_uid_map_repository->update($uid, ['chat_id' => $chat_id]);
    }

    /**
     * @param $uid
     * @return array
     */
    private function createMap($uid)
    {
        $data = [
            'sns_uid' => $uid,
            'chat_id' => $this->response_info_repository->getUniqueChatId(),
            'enquete_key' => $this->sns_uid_map_repository->getUniqueEnqueteKey(),
            'channel' => $this->channel,
        ];
        $this->sns_uid_map_repository->create($data);
        return $data;
    }

    /**
     * @param int $channel
     * @return $this
     */
    public function setChannel(int $channel)
    {
        $this->channel = $channel;
        return $this;
    }


}