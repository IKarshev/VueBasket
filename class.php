<?require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
session_start();
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;

use \Bitrix\Main\Application;
use \Bitrix\Iblock\SectionTable;
use \Bitrix\Iblock\ElementTable;
use \Bitrix\Iblock\PropertyTable;

use Bitrix\Sale;

Loader::includeModule('iblock');

class FormComponent extends CBitrixComponent implements Controllerable{

    public function configureActions(){
        // сбрасываем фильтры по-умолчанию
        return [
            'Send_Form' => ['prefilters' => [], 'postfilters' => []],
        ];
    }

    public function executeComponent(){// подключение модулей (метод подключается автоматически)
        try{
            // Проверка подключения модулей
            $this->checkModules();
            // формируем arResult
            $this->getResult();
            // подключение шаблона компонента
            $this->includeComponentTemplate();
        }
        catch (SystemException $e){
            ShowError($e->getMessage());
        }
    }

    protected function checkModules(){// если модуль не подключен выводим сообщение в catch (метод подключается внутри класса try...catch)
        if (!Loader::includeModule('iblock')){
            throw new SystemException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
        }
    }


    public function onPrepareComponentParams($arParams){//обработка $arParams (метод подключается автоматически)
        return $arParams;
    }

    protected function getResult(){ // подготовка массива $arResult (метод подключается внутри класса try...catch)
        $this->arResult = [];

        $this->arResult['ITEMS'] = self::GetBasketItem();

        return $this->arResult;
    }

    /**
     * Получаем объект корзины
     * @return Sale\Basket
     */
    private static function GetBasketObject():Bitrix\Sale\Basket
    {
        return Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    }

    /**
     * Получаем объект корзины
     * @return Sale\Basket
     */
    private static function GetBasketItem()
    {
        $basket = self::GetBasketObject();
        foreach ($basket as $basketItem) {
            $basketItems[] = [
                'ID' => $basketItem->getId(),
                'PRODUCT_ID' => $basketItem->getProductId(),
                'NAME' => $basketItem->getField('NAME'),
                'QUANTITY' => $basketItem->getQuantity(),
                'PRICE' => $basketItem->getPrice(),
                'FINAL_PRICE' => $basketItem->getFinalPrice(),
                'WEIGHT' => $basketItem->getWeight(),
                'CAN_BUY' => $basketItem->canBuy(),
                'IS_DELAY' => $basketItem->isDelay(),
                'DETAIL_PAGE_URL' => $basketItem->getField('DETAIL_PAGE_URL'),
            ];
        }

        return $basketItems;
    }

    public function Send_FormAction(){
        $request = Application::getInstance()->getContext()->getRequest();
        // получаем файлы, post
        $post = $request->getPostList();
        $files = $request->getFileList()->toArray();
        
        

        
    } 

}