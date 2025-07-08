<?php

namespace App\Exceptions;

use Exception;

class RequestError extends Exception
{
    private $responseCode;

    public function __construct($errorCode)
    {
        $this->responseCode = $errorCode;
//        если надо получать файл и строку в которой получена ошибка
//        $message = "Error in {$this->getFile()}, line: {$this->getLine()}: {$this->errorCodes[$errorCode]}";
        $message = $this->errorCodes[$errorCode] ?? $errorCode;
        parent::__construct($message);
    }

    protected $errorCodes = array(
        'ACCESS_ACTIVATION' => 'Сервис успешно активирован',
        'ACCESS_CANCEL'     => 'активация отменена',
        'ACCESS_READY'      => 'Ожидание нового смс',
        'ACCESS_RETRY_GET'  => 'Готовность номера подтверждена',
        'ACCOUNT_INACTIVE'  => 'Свободных номеров нет',
        'ALREADY_FINISH'    => 'Аренда уже завершена',
        'ALREADY_CANCEL'    => 'Аренда уже отменена',
        'BAD_ACTION'        => 'Некорректное действие (параметр action)',
        'BAD_SERVICE'       => 'Некорректное наименование сервиса (параметр service)',
        'BAD_KEY'           => 'Неверный API ключ доступа',
        'BAD_STATUS'        => 'Попытка установить несуществующий статус',
        'BANNED'            => 'Аккаунт заблокирован',
        'CANT_CANCEL'       => 'Невозможно отменить аренду (прошло более 20 мин.)',
        'ERROR_SQL'         => 'Один из параметров имеет недопустимое значение',
        'NO_NUMBERS'        => 'Нет свободных номеров для приёма смс от текущего сервиса',
        'NO_BALANCE'        => 'Закончился баланс',
        'NO_YULA_MAIL'      => 'Необходимо иметь на счету более 500 рублей для покупки сервисов холдинга Mail.ru и Mamba',
        'NO_CONNECTION'     => 'Нет соединения с серверами sms-activate',
        'NO_ID_RENT'        => 'Не указан id аренды',
        'NO_ACTIVATION'     => 'Указанного id активации не существует',
        'STATUS_CANCEL'     => 'Активация/аренда отменена',
        'STATUS_FINISH'     => 'Аренда оплачена и завершена',
        'STATUS_WAIT_CODE'  => 'Ожидание первой смс',
        'STATUS_WAIT_RETRY' => 'ожидание уточнения кода',
        'SQL_ERROR'         => 'Один из параметров имеет недопустимое значение',
        'INVALID_PHONE'     => 'Номер арендован не вами (неправильный id аренды)',
        'INCORECT_STATUS'   => 'Отсутствует или неправильно указан статус',
        'WRONG_SERVICE'     => 'Сервис не поддерживает переадресацию',
        'WRONG_SECURITY'    => 'Ошибка при попытке передать ID активации без переадресации, или же завершенной/не активной активации',
        'Failed to purchase number' => 'Не удалось приобрести номер',
        'Service not found' => 'Сервис не найден',
        'Purchase failed' => 'Покупка номера не удалась',
        'Invalid price response' => 'Неверный ответ API при получении цены',
        'Empty response from SMSPool API' => 'Пустой ответ от SMSPool API',
        'Invalid balance response' => 'Неверный ответ при проверке баланса',
        'Invalid services response' => 'Неверный ответ при получении списка сервисов',
        'Invalid countries response' => 'Неверный ответ при получении списка стран',
        'HTTP request failed: 422' => 'HTTP запрос не удался: 422 (Unprocessable Entity)',
        'HTTP request failed: 400' => 'HTTP запрос не удался: 400 (Bad Request)',
        'HTTP request failed: 401' => 'HTTP запрос не удался: 401 (Unauthorized)',
        'HTTP request failed: 403' => 'HTTP запрос не удался: 403 (Forbidden)',
        'HTTP request failed: 404' => 'HTTP запрос не удался: 404 (Not Found)',
        'HTTP request failed: 500' => 'HTTP запрос не удался: 500 (Internal Server Error)',
        'HTTP request failed: 502' => 'HTTP запрос не удался: 502 (Bad Gateway)',
        'HTTP request failed: 503' => 'HTTP запрос не удался: 503 (Service Unavailable)'
    );

    public function getResponseCode()
    {
        return $this->errorCodes[$this->responseCode] ?? $this->responseCode;
    }
}
