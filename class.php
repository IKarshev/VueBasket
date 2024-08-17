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

class VueBasket extends CBitrixComponent implements Controllerable{

    public function configureActions(){
        // сбрасываем фильтры по-умолчанию
        return [
            'ChangeItemQuantity' => ['prefilters' => [], 'postfilters' => []],
            'DeleteItem' => ['prefilters' => [], 'postfilters' => []],
            'RestoreItem' => ['prefilters' => [], 'postfilters' => []],
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
    private static function GetBasketItem():array
    {
        $basket = self::GetBasketObject();
        foreach ($basket as $basketItem) {

            $IblockItemData = array_shift(\Bitrix\Iblock\ElementTable::getList([
                'select' => ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE'],
                'filter' => ['ID' => $basketItem->getProductId()],
            ])->fetchAll());

            $arProduct = CCatalogProduct::GetByID($basketItem->getProductId());

            $basketItems[] = [
                'ID' => $basketItem->getId(),
                'PRODUCT_ID' => $basketItem->getProductId(),
                'NAME' => $basketItem->getField('NAME'),
                'QUANTITY' => $basketItem->getQuantity(),
                'MAX_QUANTITY' => $arProduct['QUANTITY'],
                'PRICE' => $basketItem->getPrice(),
                'FINAL_PRICE' => $basketItem->getFinalPrice(),
                'WEIGHT' => $basketItem->getWeight(),
                'CAN_BUY' => $basketItem->canBuy(),
                'IS_DELAY' => $basketItem->isDelay(),
                'DETAIL_PAGE_URL' => $basketItem->getField('DETAIL_PAGE_URL'),
                'PREVIEW_PICTURE' => [
                    'ID' => $IblockItemData['PREVIEW_PICTURE'],
                    'SRC' => CFile::GetPath($IblockItemData['PREVIEW_PICTURE']),
                    'RESIZE_SRC' => CFile::ResizeImageGet($IblockItemData['PREVIEW_PICTURE'], ['width' => 80, 'height' => 80], BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'],
                ],

            ];
        }

        return $basketItems;
    }

    /**
     * Увеличиваем количество в корзине
     */
    public function ChangeItemQuantityAction(){

        Loader::includeModule('iblock');
        Loader::includeModule('sale');

        $post = Application::getInstance()->getContext()->getRequest()->getPostList();

        try {
            $basket = self::GetBasketObject();
            $basketItem = $basket->getItemById($post['ID']);

            if( (int) $post['QUANTITY'] <= 0 ) throw new Exception("Кол-во не может быть меньше 1");
            
            $MaxQuantity = (int) CCatalogProduct::GetByID($basketItem->getProductId())['QUANTITY'];
            $NewQuantity = (int) ($post['QUANTITY'] <= $MaxQuantity) ? $post['QUANTITY'] : $MaxQuantity;
            $basketItem->setField('QUANTITY', $NewQuantity);
            $basketItem->save();

            return [
                'NEW_QUANTITY' => $NewQuantity,
                'MAX_QUANTITY' => $MaxQuantity,
            ];
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Удаляем товар из корзины
     */
    public function DeleteItemAction(){

        Loader::includeModule('iblock');
        Loader::includeModule('sale');

        $post = Application::getInstance()->getContext()->getRequest()->getPostList();

        try {
            if( !isset($post['ID']) || trim($post['ID']) == "" ) throw new Exception("Не корректный ID позиции товара в корзине");

            $basket = self::GetBasketObject();
            $basket->getItemById( (int)$post['ID'] )->delete();
            $basket->save();
            return;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Восстановление товара в корзине (добавление товара в корзину)
     */
    public function RestoreItemAction(){
        Loader::includeModule('iblock');
        Loader::includeModule('sale');

        $post = Application::getInstance()->getContext()->getRequest()->getPostList();

        try {
            if( !isset($post['PRODUCT_ID']) || trim($post['PRODUCT_ID']) == "" ) throw new Exception("Не корректный ID товара");
            $Quantity = $post['QUANTITY'] ?? 1;

            $basket = self::GetBasketObject();
            $item = $basket->createItem('catalog', $post['PRODUCT_ID']);
            $item->setFields(array(
                'QUANTITY' => $Quantity,
                'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ));
            $basket->save();

            return [
                'QUANTITY' => $Quantity,
            ];

        } catch (\Throwable $th) {
            return $th;
        }
    }

}