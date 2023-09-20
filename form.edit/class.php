<?
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ErrorCollection;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class RusoilFormEdit extends CBitrixComponent
{
	/** @var ErrorCollection $errors Errors. */
	protected $errors;

    protected function addError($message, $code = '')
    {
        $this->errors->setError(new \Bitrix\Main\Error($message, $code));
    }

    protected function getErrors()
    {
        $arErrors = [];
        foreach ($this->errors as $error)
        {
            $arErrors[] = $error->getMessage();
        }

        return $arErrors;
    }

    protected function printErrors()
    {
        foreach ($this->errors as $error)
        {
            ShowError($error);
        }
    }

	protected function checkRequiredParams()
	{
        return true;
	}

	protected function initParams()
	{
        $this->arParams['EMAIL_TO'] = !empty($this->arParams['EMAIL_TO']) && \check_email($this->arParams['EMAIL_TO']) ? $this->arParams['EMAIL_TO'] : \COption::GetOptionString('main','email_from');
	}

	protected function prepareResult()
	{
		return true;
	}

	private function GetValue($id,$array){
        foreach ($array as $item){
            if($item['ID'] == $id)
                return $item['NAME'];
        }
    }

    private function SaveData(){
        $title = trim(\htmlspecialcharsbx($this->request->get("title")));
        $category = intval($this->request->get("category"));
        $type = intval($this->request->get("type"));

        if(empty($title) || !$category || !$type){
            $this->addError(Loc::getMessage('ERROR_EMPTY_REQUIRED_FIELDS'));
            return false;
        }

        $store = intval($this->request->get("store"));
        $items = $this->request->get("list");
        $comment = trim(\htmlspecialcharsbx($this->request->get("comment")));

        $attachment = null;
        if(isset($_FILES['attachment']) && intval($_FILES['attachment']['size']) && !intval($_FILES['attachment']['error'])){
            $attachment = $_FILES['attachment'];
        }

        $this->arResult['VALUES'] = [
            'title' => $title,
            'category' => $category,
            'type' => $type,
            'store' => $store,
            'list' => [],
            'attachment' => $attachment,
            'comment' => $comment
        ];

        $arItems = [];
        foreach($items as $item){
            $arItems[] = [
                'brand' => intval($item['brand']),
                'name' => trim(\htmlspecialcharsbx($item['name'])),
                'quantity' => trim(\htmlspecialcharsbx($item['quantity'])),
                'packing' => trim(\htmlspecialcharsbx($item['packing'])),
                'client' => trim(\htmlspecialcharsbx($item['client'])),
            ];
        }
        $this->arResult['VALUES']['list'] = $arItems;

        if(is_array($attachment)){
            \CFile::SaveForDB($this->arResult['VALUES'], "attachment", "main");
        }

        //отправляем информацию из формы в произвольном формате на почту
        $fields = $this->arResult['VALUES'];
        $fields['category'] = $this->GetValue($fields['category'],$this->GetCategoryList());
        $fields['type'] = $this->GetValue($fields['type'],$this->GetTypeList());
        $fields['store'] = $this->GetValue($fields['store'],$this->GetStoreList());
        $fields['attachment'] = intval($this->arResult['VALUES']['attachment']) ? \CFile::GetFileArray($this->arResult['VALUES']['attachment']) : null;
        foreach($fields['list'] as &$item){
            $item['brand'] = $this->GetValue($item['brand'],$this->GetBrandList());
        }
        $arEventFields = [
            'EMAIL_TO' => $this->arParams['EMAIL_TO'],
            'FIELDS' => print_r($fields,true)
        ];

        $event = new \CEvent;
        if($event->SendImmediate('RUSOIL_FORM_RESULT', SITE_ID, $arEventFields,'Y', '', [$this->arResult['VALUES']['attachment']])){
            //Удаляем файл если не нужен
            if(intval($this->arResult['VALUES']['attachment'])){
                \CFile::Delete($this->arResult['VALUES']['attachment']);
                $this->arResult['VALUES']['attachment'] = 'deleted';
            }

            return true;
        }else{
            $this->addError(Loc::getMessage('ERROR_SEND_MESSAGE'));
        }

        return false;
    }

    private function GetArrayFromSelectBox($array){
        //Преобразуем массив в вид для функции SelectBoxFromArray
        $arSelect = [];
        foreach($array as $category){
            $arSelect['REFERENCE'][] = $category['NAME'];
            $arSelect['REFERENCE_ID'][] = $category['ID'];
        }
        return $arSelect;
    }

    private function GetCategoryList(){
	    //Здесь делаем запрос для получения списка категорий и формируем массив
        //Для упрощения я сформирую массив в коде, в том варианте в котором он обычно приходит
        $arCategories = [];
        $arCategories[] = [
            'ID' => 1,
            'NAME' => 'Масла, автохимия, фильтры'
        ];
        $arCategories[] = [
            'ID' => 2,
            'NAME' => 'Шины, диски'
        ];

        return $arCategories;
    }

    private function GetTypeList(){
	    //Здесь делаем запрос для получения списка видов заявок и формируем массив
        //Для упрощения я сформирую массив в коде, в том варианте в котором он обычно приходит
        $arTypes = [];
        $arTypes[] = [
            'ID' => 1,
            'NAME' => 'Запрос цены и сроков поставки'
        ];
        $arTypes[] = [
            'ID' => 2,
            'NAME' => 'Пополнение складов'
        ];
        $arTypes[] = [
            'ID' => 3,
            'NAME' => 'Спецзаказ'
        ];

        return $arTypes;
    }

    private function GetStoreList(){
	    //Здесь делаем запрос для получения списка видов заявок и формируем массив
        //Для упрощения я сформирую массив в коде, в том варианте в котором он обычно приходит
        $arStore = [];
        $arStore[] = [
            'ID' => 1,
            'NAME' => 'Склад 1'
        ];
        $arStore[] = [
            'ID' => 2,
            'NAME' => 'Склад 2'
        ];
        $arStore[] = [
            'ID' => 3,
            'NAME' => 'Склад 3'
        ];

        return $arStore;
    }

    private function GetBrandList(){
	    //Здесь делаем запрос для получения списка видов заявок и формируем массив
        //Для упрощения я сформирую массив в коде, в том варианте в котором он обычно приходит
        $arBrand = [];
        $arBrand[] = [
            'ID' => 1,
            'NAME' => 'Бренд 1'
        ];
        $arBrand[] = [
            'ID' => 2,
            'NAME' => 'Бренд 2'
        ];
        $arBrand[] = [
            'ID' => 3,
            'NAME' => 'Бренд 3'
        ];

        return $arBrand;
    }

    private function GetData(){
        if(empty($this->arResult['RESULT']) && $this->request->get("result") == 'ok'){
            $this->arResult['RESULT'] = 'ok';
        }

        $this->arResult['CATEGORY_LIST'] = $this->GetCategoryList();
        $this->arResult['TYPE_LIST'] = $this->GetTypeList();
        $arStores = $this->GetStoreList();
        $this->arResult['STORE_LIST'] = $this->GetArrayFromSelectBox($arStores);
        $arBrand = $this->GetBrandList();
        $this->arResult['BRAND_LIST'] = $this->GetArrayFromSelectBox($arBrand);
    }

	public function executeComponent()
	{
	    global $APPLICATION;

        $this->errors = new ErrorCollection();
		$this->initParams();
        
		if (!$this->checkRequiredParams())
		{
			$this->printErrors();
			return;
		}

        if($this->request->isPost() && check_bitrix_sessid()){
            $result = $this->SaveData();
            if($result)
                LocalRedirect($APPLICATION->GetCurPageParam("result=ok",['result'],false));
            else {
                $this->arResult['RESULT'] = 'error';
                $this->arResult['RESULT_MESSAGE'] = join('<br />',$this->getErrors());
            }
        }
        
        $this->GetData();

		if (!$this->prepareResult())
		{
			$this->printErrors();
			return;
		}

		$this->includeComponentTemplate();
	}
}