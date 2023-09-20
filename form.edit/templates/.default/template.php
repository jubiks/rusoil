<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var CAllMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */
/** @var \CBitrixComponentTemplate $this */

use Bitrix\Main\Localization\Loc;

CJSCore::Init(array("jquery"));
$this->addExternalCss('https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css');
$this->addExternalJs('https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js');
?>
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-5"><?=Loc::getMessage('TITLE_FORM');?></h1>
            <?if($arResult['RESULT'] == 'ok'):?>
                <div class="alert alert-success" role="alert"><?=Loc::getMessage('ALERT_SUCCESS');?></div>
            <?elseif($arResult['RESULT'] == 'error'):?>
                <div class="alert alert-danger" role="alert"><?=$arResult['RESULT_MESSAGE']?></div>
            <?endif?>
            <form action="<?=$APPLICATION->GetCurPageParam('',[],false)?>" method="post" enctype="multipart/form-data">
                <?=bitrix_sessid_post()?>
                <div class="mb-4">
                    <label for="InputTitle" class="form-label form-required"><?=Loc::getMessage('INPUT_TITLE');?></label>
                    <input type="text" name="title" class="form-control" id="InputTitle" value="<?=$arResult['VALUES']['title']?>" required>
                </div>

                <div class="mb-4">
                    <h2 class="mb-3 form-required"><?=Loc::getMessage('INPUT_CATEGORY');?></h2>
                    <?foreach($arResult['CATEGORY_LIST'] as $category):?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" value="<?=$category['ID']?>" id="category_<?=$category['ID']?>" required <?=$category['ID'] == $arResult['VALUES']['category'] ? 'checked' : ''?>>
                        <label class="form-check-label" for="category_<?=$category['ID']?>"><?=$category['NAME']?></label>
                    </div>
                    <?endforeach?>
                </div>

                <div class="mb-4">
                    <h2 class="mb-3 form-required"><?=Loc::getMessage('INPUT_TYPE');?></h2>
                    <?foreach($arResult['TYPE_LIST'] as $type):?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="<?=$type['ID']?>" id="type_<?=$type['ID']?>" required <?=$type['ID'] == $arResult['VALUES']['type'] ? 'checked' : ''?>>
                        <label class="form-check-label" for="type_<?=$type['ID']?>"><?=$type['NAME']?></label>
                    </div>
                    <?endforeach?>
                </div>

                <div class="mb-4">
                    <h2 class="mb-3"><?=Loc::getMessage('INPUT_STORE');?></h2>
                    <?=SelectBoxFromArray("store",$arResult['STORE_LIST'],$arResult['VALUES']['store'],Loc::getMessage('INPUT_STORE_DEFAULT_SELECT'),"class=\"form-select\"")?>
                </div>

                <div class="mb-4">
                    <h2 class="mb-3"><?=Loc::getMessage('TABLE_ITEMS');?></h2>
                    <div class="list-items">
                        <div class="list-header">
                            <div class="list-header-th"><?=Loc::getMessage('TABLE_ITEMS_HEADER_BRAND');?></div>
                            <div class="list-header-th"><?=Loc::getMessage('TABLE_ITEMS_HEADER_NAME');?></div>
                            <div class="list-header-th"><?=Loc::getMessage('TABLE_ITEMS_HEADER_QUANTITY');?></div>
                            <div class="list-header-th"><?=Loc::getMessage('TABLE_ITEMS_HEADER_PACKING');?></div>
                            <div class="list-header-th"><?=Loc::getMessage('TABLE_ITEMS_HEADER_CLIENT');?></div>
                            <div class="list-header-th list-row-action">&nbsp;</div>
                        </div>
                        <div class="list-body" data-rows="<?=count($arResult['VALUES']['list'])?>">
                            <?
                            $num = 0;
                            do {
                            ?>
                            <div class="list-body-row" data-row="<?=$num?>">
                                <div class="list-row-td">
                                    <label for="list_brand_<?=$num?>" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_BRAND');?></label>
                                    <?=SelectBoxFromArray("list[".$num."][brand]",$arResult['BRAND_LIST'],$arResult['VALUES']['list'][$num]['brand'],Loc::getMessage('TABLE_ITEMS_HEADER_BRAND_DEFAULT_SELECT'),"class=\"form-select\" id=\"list_brand_".$num."\"")?>
                                </div>
                                <div class="list-row-td">
                                    <label for="list_name_<?=$num?>" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_NAME');?></label>
                                    <input type="text" name="list[<?=$num?>][name]" class="form-control" id="list_name_<?=$num?>" value="<?=$arResult['VALUES']['list'][$num]['name']?>">
                                </div>
                                <div class="list-row-td">
                                    <label for="list_quantity_<?=$num?>" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_QUANTITY');?></label>
                                    <input type="text" name="list[<?=$num?>][quantity]" class="form-control" id="list_quantity_<?=$num?>" value="<?=$arResult['VALUES']['list'][$num]['quantity']?>">
                                </div>
                                <div class="list-row-td">
                                    <label for="list_packing_<?=$num?>" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_PACKING');?></label>
                                    <input type="text" name="list[<?=$num?>][packing]" class="form-control" id="list_packing_<?=$num?>" value="<?=$arResult['VALUES']['list'][$num]['packing']?>">
                                </div>
                                <div class="list-row-td">
                                    <label for="list_client_<?=$num?>" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_CLIENT');?></label>
                                    <input type="text" name="list[<?=$num?>][client]" class="form-control" id="list_client_<?=$num?>" value="<?=$arResult['VALUES']['list'][$num]['client']?>">
                                </div>
                                <div class="list-row-td list-row-action">
                                    <a href="javascript:void(0);" class="icon-link" title="<?=Loc::getMessage('TABLE_ITEMS_ROW_DELETE_TITLE');?>" onclick="listRowDel(<?=$num?>);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <?} while(key_exists(++$num, $arResult['VALUES']['list']))?>
                        </div>
                        <div class="list-footer">
                            <div class="action-block">
                                <button type="button" class="btn btn-secondary btn-sm-block" onclick="listRowAdd();">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                    <span><?=Loc::getMessage('TABLE_ITEMS_ROW_ADD_TEXT');?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <script type="text/html" class="list-row-template">
                        <div class="list-body-row" data-row="{{num}}">
                            <div class="list-row-td">
                                <label for="list_brand_{{num}}" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_BRAND');?></label>
                                <?=SelectBoxFromArray("list[{{num}}][brand]",$arResult['BRAND_LIST'],"",Loc::getMessage('TABLE_ITEMS_HEADER_BRAND_DEFAULT_SELECT'),"class=\"form-select\" id=\"list_brand_{{num}}\"")?>
                            </div>
                            <div class="list-row-td">
                                <label for="list_name_{{num}}" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_NAME');?></label>
                                <input type="text" name="list[{{num}}][name]" class="form-control" id="list_name_{{num}}">
                            </div>
                            <div class="list-row-td">
                                <label for="list_quantity_{{num}}" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_QUANTITY');?></label>
                                <input type="text" name="list[{{num}}][quantity]" class="form-control" id="list_quantity_{{num}}">
                            </div>
                            <div class="list-row-td">
                                <label for="list_packing_{{num}}" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_PACKING');?></label>
                                <input type="text" name="list[{{num}}][packing]" class="form-control" id="list_packing_{{num}}">
                            </div>
                            <div class="list-row-td">
                                <label for="list_client_{{num}}" class="item-label"><?=Loc::getMessage('TABLE_ITEMS_HEADER_CLIENT');?></label>
                                <input type="text" name="list[{{num}}][client]" class="form-control" id="list_client_{{num}}">
                            </div>
                            <div class="list-row-td list-row-action">
                                <a href="javascript:void(0);" class="icon-link" title="<?=Loc::getMessage('TABLE_ITEMS_ROW_DELETE_TITLE');?>" onclick="listRowDel({{num}});">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </script>
                </div>

                <div class="mb-4">
                    <input type="file" name="attachment" class="form-control">
                </div>

                <div class="mb-4">
                    <label for="textComment"><?=Loc::getMessage('INPUT_COMMENT');?></label>
                    <textarea name="comment" id="textComment" rows="10" class="form-control"><?=$arResult['VALUES']['comment']?></textarea>
                </div>

                <div class="form-action">
                    <button type="submit" name="submit" value="send" class="btn btn-success btn-sm-block"><?=Loc::getMessage('SUBMIT_BUTTON_TEXT');?></button>
                </div>
            </form>
        </div>
    </div>
</div>

