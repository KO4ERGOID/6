<?php

Class IBlockTypeOperations {
   public function ViewBlockTypeInformation($IBlockTypeId, $IBlock) 
   {

      $res = CIBlock::GetList(
         Array(), 
         Array(
             'TYPE'=>$IBlockTypeId, 
             'SITE_ID'=>'s1', 
             'ACTIVE'=>'Y', 
             "CNT_ACTIVE"=>"Y", 
         ), true
     );
     while($ar_res = $res->Fetch())
     {
         if ($ar_res['NAME']==$IBlock) 
         {
            $IBlockID = $ar_res['ID'];
         }
     }

      $res = CIBlock::GetList(
         Array(), 
         Array(
             'TYPE'=>$IBlockTypeId, 
             'SITE_ID'=>'s1', 
             'ACTIVE'=>'Y', 
             "CNT_ACTIVE"=>"Y", 
         ), true
      );
      $AvailabilityIBlockID=0;
      while($ar_res = $res->Fetch())
      {
        if ($ar_res['NAME'] == $IBlock) {
         $AvailabilityIBlockName++;
        } 
      }

       if($AvailabilityIBlockName==0) {
      $res = CIBlock::GetList(
          Array(), 
          Array(
              'TYPE'=>$IBlockTypeId, 
              'SITE_ID'=>'s1', 
              'ACTIVE'=>'Y', 
              "CNT_ACTIVE"=>"Y", 
          ), true
       );
       echo '<br>Список инфоблоков выбранного типа: <br><br>';
       while($ar_res = $res->Fetch())
       {
         echo '<br>'.'Название инфоблока: '.$ar_res['NAME'].'.<br>Количество элементов: '.$ar_res['ELEMENT_CNT'].'<br>';
         $arSelect = Array("NAME");
         $arFilter = Array("IBLOCK_ID"=>$ar_res['ID']);
         $res2 = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>100), $arSelect);
         while($ob = $res2->GetNextElement())
         {
         $arFields = $ob->GetFields();
          echo '<pre>'; echo $arFields['NAME'];  echo '</pre>'; 
         } 
       }
     } else {
      $arSelect = Array("NAME");
      $arFilter = Array("IBLOCK_ID"=>$IBlockID);
      $res2 = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>100), $arSelect);
      while($ob = $res2->GetNextElement())
      {
      $arFields = $ob->GetFields();
       echo '<pre>'; echo 'El: '.$arFields['NAME'];  echo '</pre>'; 
      } 
     }
   } 
   }


//Интерфейс выбора типа инфоблока
$db_iblock_type = CIBlockType::GetList();
echo "<form method='GET'>";
echo "Тип инфоблока: <select name='IBlockTypeId'>";
while($ar_iblock_type = $db_iblock_type->Fetch())
{  
   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
   {
      echo "<option>"; echo $arIBType['IBLOCK_TYPE_ID']."<br>"; echo "</option>";
   }   
}
echo "</select>";
$IBlockTypeId = $_GET['IBlockTypeId'];
if ($IBlockTypeId!=NULL) {
   $res = CIBlock::GetList(
      Array(), 
      Array(
          'TYPE'=>$IBlockTypeId, 
          'SITE_ID'=>'s1', 
          'ACTIVE'=>'Y', 
          "CNT_ACTIVE"=>"Y", 
      ), true
  );
  echo "<br><br>Инфоблок: <select name='IBlock'>";
  while($ar_res = $res->Fetch())
  {
   echo "<option>"; echo $ar_res['NAME']; echo "</option>";
  }
  echo "</select><br><br>";

}
echo "<input type='submit' value='Выбрать'>";
echo "</form>";
echo '<br> ID Выбранного типа инфоблока: '.$IBlockTypeId.'<br>';
$IBlock = $_GET['IBlock'];
echo '<br> Выбранный инфоблок: '.$IBlock.'<br>';




IBlockTypeOperations::ViewBlockTypeInformation($IBlockTypeId, $IBlock); 



//Сортировка в $arResult['ITEMS']
$arResult['ITEMS']= array();
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
   {
      //echo '<br> Тип: '.$ar_iblock_type["ID"];
      $res = CIBlock::GetList(
         Array(), 
         Array(
             'TYPE'=>$ar_iblock_type["ID"], 
             'SITE_ID'=>$SiteId, 
             'ACTIVE'=>'Y', 
             "CNT_ACTIVE"=>"Y", 
         ), true
      );
      while($ar_res = $res->Fetch())
      {
               //echo '<br> Инфоблок: '; echo $ar_res['NAME'];       
                  $arSelect = Array("ID", "NAME");
                  $arFilter = Array("IBLOCK_ID"=>$ar_res['ID'], 'SECTION_ID' =>$ar_result['ID'] );
                  $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>500), $arSelect);
                  while($ob = $res->GetNextElement())
                  {
                  $arFields = $ob->GetFields();
                 
                        $res2 = CIBlockElement::GetByID($arFields['ID']);
                        if($ar_res = $res2->GetNext())
                        {
                        $arResult['ITEMS'][$ar_res['IBLOCK_ID']][$ar_res['ID']] = $ar_res;
                        //echo '<pre>'; print_r('Элемент: '.$ar_res['NAME'].' ID:'.$ar_res['ID']); echo '</pre>';
                        //echo '<pre>'; print_r($ar_res); echo '</pre>';
                        }            
      }
   }
}
} 
//echo '<pre>'; print_r($arResult['ITEMS']['3']); echo '</pre>';

?>

