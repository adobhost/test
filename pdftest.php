<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Mpdf\Mpdf as PDF;
use Illuminate\Support\Facades\Storage;
use Hijri;
use ArabicNumbers;
use Illuminate\Support\Facades\DB;
use App\Models\SummaryTable;
use App\Models\Label;

/*

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}

function valueToHex($string){
    $hex = '';

        $hexCode = dechex($string);
        $hex .= substr('0'.$hexCode, -2);

        return strToUpper($hex);
}

function hex_to_base64($str){
  $return = '';
  foreach(str_split($str, 2) as $pair){
    $return .= chr(hexdec($pair));
  }
  return base64_encode($return);
}


$secT =  valueToHex('1');
$secL =  valueToHex(strlen($Cl_Organization_name));
//echo $Cl_Organization_name;
$secV =  strToHex($Cl_Organization_name);
//echo $secV ;

$str1=  $secT.$secL.$secV;
$str .= $str1 ;

$secT =  valueToHex('2');
$secL =  valueToHex(strlen($tax_number));
$secV =  strToHex($tax_number);

$str1=  $secT.$secL.$secV;
$str .= $str1 ;

//$date = '2022-04-25T15:30:00Z';
$date = $r14.' '. $time;
$secT =  valueToHex('3');
$secL =  valueToHex(strlen($date));
$secV =  strToHex($date);

$str1=  $secT.$secL.$secV;
$str .= $str1 ;


$secT =  valueToHex('4');
$secL =  valueToHex(strlen($Total_bill));
$secV =  strToHex($Total_bill);

$str1=  $secT.$secL.$secV;
$str .= $str1 ;

$secT =  valueToHex('5');
$secL =  valueToHex(strlen($vat_amount));
$secV =  strToHex($vat_amount);

$str1=  $secT.$secL.$secV;
$str .= $str1 ;

$code = hex_to_base64($str);


*/

class pdftest extends Controller
{
    //


    public function generate_pdf($page, $rep, $id, $type)
    {


        $report_post_id = 5018;
        $rep_id = $rep;
        $page_id = $page;
        $master_id = $id;


        $str = "";

        function strToHex($string)
        {
            $hex = '';
            for ($i = 0; $i < strlen($string); $i++) {
                $ord = ord($string[$i]);
                $hexCode = dechex($ord);
                $hex .= substr('0' . $hexCode, -2);
            }
            return strToUpper($hex);
        }

        function valueToHex($string)
        {
            $hex = '';

            $hexCode = dechex($string);
            $hex .= substr('0' . $hexCode, -2);

            return strToUpper($hex);
        }

        function hex_to_base64($str)
        {
            $return = '';
            foreach (str_split($str, 2) as $pair) {
                $return .= chr(hexdec($pair));
            }
            return base64_encode($return);
        }


        $Cl_Organization_name = 'mtsc';
        $tax_number = '10';
        $date = '2022-04-25T15:30:00Z';
        $Total_bill = '550';
        $vat_amount = '50';


        $secT = valueToHex('1');
        $secL = valueToHex(strlen($Cl_Organization_name));
        //echo $Cl_Organization_name;
        $secV = strToHex($Cl_Organization_name);
        //echo $secV ;

        $str1 = $secT . $secL . $secV;
        $str .= $str1;

        $secT = valueToHex('2');
        $secL = valueToHex(strlen($tax_number));
        $secV = strToHex($tax_number);

        $str1 = $secT . $secL . $secV;
        $str .= $str1;

        //$date = '2022-04-25T15:30:00Z';
        // $date = $r14.' '. $time;
        $secT = valueToHex('3');
        $secL = valueToHex(strlen($date));
        $secV = strToHex($date);

        $str1 = $secT . $secL . $secV;
        $str .= $str1;


        $secT = valueToHex('4');
        $secL = valueToHex(strlen($Total_bill));
        $secV = strToHex($Total_bill);

        $str1 = $secT . $secL . $secV;
        $str .= $str1;

        $secT = valueToHex('5');
        $secL = valueToHex(strlen($vat_amount));
        $secV = strToHex($vat_amount);

        $str1 = $secT . $secL . $secV;
        $str .= $str1;

        $code = hex_to_base64($str);


        $orginfo = DB::table("tb_organization")
            ->select('report_logo', 'logo_height', 'report_footer')
            ->where('cl_organization_id', session('orgId'))
            ->first();

        $report_logo = $orginfo->report_logo;
        $logo_height = $orginfo->logo_height;
        $report_footer_img = $orginfo->report_footer;


        if (empty($report_footer_img)) {
            $report_footer = '';
        } else {
            $report_footer = ' <img  style="width: 100%; height: 125px;" src="org_image/' . $report_footer_img . '">';
        }

        $detailrep = DB::table("rep_builder_names")
            ->where('id', $rep_id)
            ->first();

        if (session('language') == 'rtl') {
            $font_ar = 'ar_name';
            $pagename = 'cl_title_ar';
        } else {
            $font_ar = 'en_name';
            $pagename = 'cl_title_en';
        }

        if ($rep_id == 207) {

            if (session('language') == 'rtl') {
                $rep_name_state = 'ar_name';
            } else {
                $rep_name_state = 'en_name';
            }
            $rep_state = DB::table("sale_order_master_tb")
                ->where('id', $master_id)
                ->pluck('sale_status')
                ->first();

            $rep_class_name = DB::table("tb_constant_status")
                ->where('id', $rep_state)
                ->pluck($rep_name_state)
                ->first();


        } else {

            $rep_class_name = DB::table("tb_pages")
                ->where('cl_page_id', $page_id)
                ->pluck($pagename)
                ->first();
        }

        $print_user = $detailrep->print_user;
        $print_date = $detailrep->print_date;
        $paper_type = $detailrep->paper_type;
        $orientation = $detailrep->orientation;
        $multi_lang = $detailrep->multi_lang;
        $print_header = $detailrep->print_header;
        $print_footer = $detailrep->print_footer;
        $font_name = $detailrep->font_name;
        $font_size = $detailrep->font_size;


        if ($print_user == 33) {
            $user_name = $detailrep->user_name;
        } else {
            $user_name = '';
        }

        if ($print_date == 33) {
            $today = Date('Y-m-d');
        } else {
            $today = '';
        }


        $font_size = DB::table("tb_constant_status")
            ->where('id', $font_size)
            ->pluck($font_ar)
            ->first();

        $font_name = DB::table("tb_constant_status")
            ->where('id', $font_name)
            ->pluck($font_ar)
            ->first();

        $paper_type = DB::table("tb_constant_status")
            ->where('id', $paper_type)
            ->pluck($font_ar)
            ->first();

        $orientation = DB::table("tb_constant_status")
            ->where('id', $orientation)
            ->pluck('mapp')
            ->first();


        $rowid = DB::table("reports_roles as rr")
            ->select('plr.ar_col_name', 'plr.label_id', 'tl.f_in_db')
            ->leftJoin("page_label_report as plr", "rr.column_id", "=", "plr.id")
            ->leftJoin("tb_labels as tl", "plr.label_id", "=", "tl.Cl_ID")
            ->where('plr.column_state', 530)
            ->where('rr.report_custom_name', $rep_id)
            ->where('rr.user_name', session('userName'))
            ->where('plr.hidden_column', 34)
            ->whereIn('rr.column_role', array(532, 534))
            ->get();


        foreach ($rowid as $row) {
            $parameters[] = $row->f_in_db;
            $header_columns[] = $row->ar_col_name;
            $labels_array[] = $row->label_id;
        }

        // dd($header_columns);
        $header_string = implode(",", $header_columns);

        $cols_count = 2;
        $j = 0;


        if ($rep_id == 207) {
            $headercol = DB::table("sale_order_master_tb as somt")
                ->selectraw($header_string)
                ->Join("salesman_tb as smt", "somt.sale_man", "=", "smt.id")
                ->Join("customer_sale as cs", "somt.customer_name", "=", "cs.cust_id")
                ->Join("tb_currency as tc", "somt.order_currency", "=", "tc.cl_currency_code")
                ->where('somt.id', $master_id)
                ->get();


        }

        if ($rep_id == 169) {
            $headercol = DB::table("invoiceb_stock_master_tb as ismt")
                ->selectraw($header_string)
                ->leftJoin("tb_supplier as ts", "ismt.supplier_id", "=", "ts.Cl_id")
                ->leftJoin("representatives_tb as rt", "ismt.representative", "=", "rt.id")
                ->where('ismt.id', $master_id)
                ->get();
        }


        if ($rep_id == 200) {
            $headercol = DB::table("correction_stock_master_tb as csm")
                ->selectraw($header_string)
                ->join("transeaction_type_tb as tt", "tt.id", "=", "csm.transeaction_type")
                ->join("tb_organization as o", "o.Cl_Organization_id", "=", "csm.Cl_Organization_id")
                ->join("warehouse_guard_tb as wg", "wg.id", "=", "csm.warehouse_guard_id")
                ->where('csm.id', $master_id)
                ->get();
        }

        if ($rep_id == 143) {

            $headercol = DB::table("quotation_master_tb as qmtb")
                ->selectraw($header_string)
                ->leftJoin("customer_sale as cs", "qmtb.customer_name", "=", "cs.cust_id")
                ->leftJoin("salesman_tb as st", "qmtb.sale_man", "=", "st.id")
                ->leftJoin(
                    DB::raw('(select cssp.speech_id,qmt.id from
quotation_master_tb qmt left join  customer_sale_speech_person  cssp
on qmt.customer_name = cssp.cust_id
where qmt.id = ' . $master_id . '
and cssp.is_default=1 ) temp'),
                    function ($join) {
                        $join->on("temp.id", "=", "qmtb.id");
                    }
                )
                ->leftJoin("customer_sale as cs1", "temp.speech_id", "=", "cs1.cust_id")
                ->where('qmtb.id', $master_id)
                ->get();


        }

        // $formData = $headercol->toArray();
        $formData = json_encode($headercol);
        $formData = json_decode($formData);


        $tableheader = '<table style="text-align: center;" width="100%" border="1" cellspacing="1" cellpadding="2" ><tbody>';

        foreach ($formData as $key => $item) {

            $columns = collect($item)->toArray();
        }
        // dd($columns);

        $new = $labels_array;
        $newarray = array_combine($new, $columns);
        //  dd($newarray);
        foreach ($newarray as $key => $item) {


            if ($multi_lang == 33) {
                $labelname = Label::find($key);
                $labelname = $labelname->cl_ar_name . '-' . $labelname->cl_en_name;
            } else {
                $labelname = Label::find($key);

                if (session('language') == 'rtl') {
                    $label = 'cl_ar_name';
                } else {
                    $label = 'cl_en_name';
                }

                $labelname = $labelname->$label;
            }

            // dd($labelname);
            // dump($item);
            if ($j % $cols_count == 0) {
                $tableheader .= '<tr>';
            }
            $tableheader .= '<td style="text-align:center; background-color: whitesmoke; color: black">' . $labelname . '</td>
 		<td style="text-align:center;">' . $item . '</td>';

            if ($j % $cols_count != 0) {
                $tableheader .= '</tr>';
            }
            $j++;
        }
        //    dd($header_columns,$labels_array,$parameters);

        if (!empty($tableheader)) {
            $tableheader .= '</tbody></table>';
        }

        $rbid = DB::table("reports_roles as rr")
            ->select('plr.ar_col_name', 'rr.id', 'plr.label_id', 'tl.f_in_db', 'rr.col_width')
            ->leftJoin("page_label_report as plr", "rr.column_id", "=", "plr.id")
            ->leftJoin("tb_labels as tl", "plr.label_id", "=", "tl.Cl_ID")
            ->whereIn('plr.column_state', array(514, 531))
            ->where('rr.report_custom_name', $rep_id)
            ->where('rr.user_name', session('userName'))
            ->where('plr.hidden_column', 34)
            ->whereIn('rr.column_role', array(532, 534))
            ->orderby('plr.rank', 'asc')
            ->get();


        //  dd($rbid);


        foreach ($rbid as $row) {
            $body_width[] = $row->col_width;
            $body_header[] = $row->ar_col_name;
            $body_label_id[] = $row->label_id;

            $body_f_in_db[] = $row->f_in_db;
        }

        // dd($rbid,$body_header,$body_label_id);

        if ($rep_id == 207) {

            $string = implode(",", $body_header);
            $string = str_replace('__toFixed__', session('toFixed'), $string);

            $bodycol = DB::table("sale_order_details_tb as sodt")
                ->selectraw($string)
                ->join("sale_order_master_tb as somt", "somt.id", "=", "sodt.master_id")
                ->join("tb_item as ti", "ti.Cl_index", "=", "sodt.cl_index")
                ->where('sodt.page_id', $page_id)
                ->where('somt.id', $master_id)
                ->get();

            $array = [
                'tb_Detailes' => 'sale_order_details_tb',
                'tb_Master' => 'sale_order_master_tb',
                'quantity' => 'quantity_out',
                'masterId' => $master_id,
                'pageId' => $page_id,
                'print' => 'no'
            ];

        }

        if ($rep_id == 143) {

            $string = implode(",", $body_header);
            $string = str_replace('__toFixed__', session('toFixed'), $string);

            $bodycol_1 = DB::table("quotation_details_tb as sodt")
                ->selectraw($string)
                ->join("tb_item as ti", "sodt.Cl_index", "=", "ti.Cl_index")
                ->leftJoin("tb_currency as tc", "ti.Cl_Country_id", "=", "tc.country_code")
                ->where('sodt.quotation_item', '0')
                ->where('sodt.master_id', $master_id);


            $bodycol = DB::table("quotation_details_tb as sodt")
                ->selectraw($string)
                ->join("quotation_item_tb as ti", "sodt.Cl_index", "=", "ti.Cl_index")
                ->leftJoin("tb_currency as tc", "ti.Cl_Country_id", "=", "tc.country_code")
                ->where('sodt.quotation_item', '0')
                ->where('sodt.master_id', $master_id)
                ->union($bodycol_1)
                ->get();

            $array = [
                'tb_Detailes' => 'quotation_details_tb',
                'tb_Master' => 'quotation_item_tb',
                'quantity' => 'quantity_out',
                'masterId' => $master_id,
                'pageId' => $page_id,
                'print' => 'no'
            ];

        }

        if ($rep_id == 169) {

            $string = implode(",", $body_header);
            $string = str_replace('__toFixed__', session('toFixed'), $string);
            $bodycol = DB::table("invoiceb_stock_detailes_tb as isdt")
                ->selectraw($string)
                ->leftJoin("tb_items_join_itemsuppliers as tiii", "isdt.Cl_index", "=", "tiii.Cl_index")
                ->leftJoin("tb_item_supplier_definition as tisd", "tisd.id", "=", "tiii.item_supplier_id")
                ->Join("invoiceb_stock_master_tb as ismt", "ismt.id", "=", "isdt.master_id")
                ->Join("tb_item as ti", "ti.Cl_index", "=", "isdt.Cl_index")
                ->Join("tb_units as tu", "isdt.unit", "=", "tu.id")
                ->Join("representatives_tb as rt", "ismt.representative", "=", "rt.id")
                ->where('isdt.branch_no', session('orgId'))
                ->where('isdt.page_id', $page_id)
                ->where('ismt.id', $master_id)
                ->orderby('isdt.id', 'asc')
                ->get();


            $array = [
                'tb_Detailes' => 'invoiceb_stock_detailes_tb',
                'tb_Master' => 'invoiceb_stock_master_tb',
                'quantity' => 'quantity_in',
                'masterId' => $master_id,
                'pageId' => $page_id,
                'print' => 'no'
            ];

        }

        if ($rep_id == 200) {

            $string = implode(",", $body_header);
            $string = str_replace('__toFixed__', session('toFixed'), $string);
            $bodycol = DB::table("correction_stock_detailes_tb as csdt")
                ->selectraw($string)
                ->join("tb_item as ti", "csdt.Cl_index", "=", "ti.Cl_index")
                ->Join("correction_stock_master_tb as csm", "csm.id", "=", "csdt.master_id")
                ->Join("tb_units as tu", "csdt.unit", "=", "tu.id")
                ->Join("transeaction_type_tb as tt", "csm.transeaction_type", "=", "tt.id")
                ->Join("stores_tb as st", "csdt.store_id", "=", "st.id")
                ->leftJoin("additional_information_tb as ait", "ait.master_id", "=", "csdt.id")
                ->where('csdt.branch_no', session('orgId'))
                ->where('csm.id', $master_id)
                ->orderby('csm.id', 'asc')
                ->get();


            /*$array = [
            'tb_Detailes' => 'invoiceb_stock_detailes_tb',
            'tb_Master'   => 'invoiceb_stock_master_tb',
            'quantity'    => 'quantity_out',
            'masterId'    => $master_id,
            'pageId'    => $page_id,
            'print' => 'no'
        ];*/

        }


        foreach ($bodycol as $key => $item) {
            $bodycolumns = collect($item)->toArray();

            $newlabel = $body_label_id;
            $labelcolumns = collect($newlabel)->toArray();
            $bodywidth = collect($body_width)->toArray();

            $newbodyarray[] = array_combine($newlabel, $bodycolumns);
            $bodycol[] = collect($item)->toArray();
        }
        // dd( $newlabel,$bodycolumns,$newbodyarray);

        // dd($bodycol,$body_header,$body_label_id);


        $widtharray = array_combine($newlabel, $body_width);
        $tablebody = '<table style="text-align: center;" width="100%" border="1" ><tbody>';
        $tablebody .= '<tr>';
        // dd($widtharray);

        foreach ($widtharray as $key => $labelitems) {

            if ($multi_lang == 33) {
                $labelname = Label::find($key);
                $labelname = $labelname->cl_ar_name . '-' . $labelname->cl_en_name;
            } else {
                $labelname = Label::find($key);

                if (session('language') == 'rtl') {
                    $label = 'cl_ar_name';
                } else {
                    $label = 'cl_en_name';
                }

                $labelname = $labelname->$label;
            }

            $tablebody .= '<td style="text-align:center;width:' . $labelitems . '%; background-color: whitesmoke; color: black">' . $labelname . '</td>';
        }
        $tablebody .= '</tr>';

        foreach ($newbodyarray as $items) {

            // dump($item);

            $tablebody .= '<tr>';

            foreach ($items as $key => $item) {

                $tablebody .= '<td style="text-align:center;">' . $item . '</td>';
            }

            $tablebody .= '</tr>';
        }  //    dd($header_columns,$labels_array,$parameters);
        if (!empty($tablebody)) {
            $tablebody .= '</tbody></table>';
        }


        $content_table = '';
        if ($rep_id != 200) {


            $tableBodyPlus = SummaryTable::get($array);


            $pr_code_detail = '';
            foreach ($tableBodyPlus as $key => $value) {
                $labelname = Label::name($key);

                $pr_code_detail .= '' . $labelname . '=>' . $value . '';
            };

            $content = rawurlencode($code);
            $qr_code = '  <img src="https://api.qrserver.com/v1/create-qr-code/?data=' . $content . '&size=220x220&margin=0" alt=""  width="150" height="150" />';


            $content_table = '<table border="1"  width="100%">';

            $content_table .= '<tr>

            <td align="center" rowspan="10">' . $qr_code . '</td></tr>';

            foreach ($tableBodyPlus as $key => $value) {
                $labelname = Label::name($key);

                $content_table .= '<tr>
     <td align="center" >' . $labelname . '</td>
     <td align="center">' . $value . '</td></tr>';
            }


            $content_table .= '</table>';

        }
        $documentFileName = "table-$today.pdf";


        // Set some header informations for output
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $documentFileName . '"'
        ];


        $headerhome = '
<img  style="width: 100%; height: 125px;" src="org_image/' . $report_logo . '">
';

        $headerdetail = '
<table style="text-align: center;" width="100%" border="0" cellspacing="1" cellpadding="2">
<tbody>
<tr><td>' . $user_name . '
</td><td>
</td><td>' . $today . '
</td>
</tr>
<tr><td>
</td>
<td width="40%" style="background-color:#565656; color: #ffffff;"><span>' . $rep_class_name . '</span>
</td>
<td></td>
</tr>
<tr><td>
</td><td>
</td><td>
</td></tr>
</tbody></table>
';

        $headerfooter = '
' . $report_footer . '

        <div style="border-top: 1px solid #000000;  padding-top: 3mm; ">

        <table style="text-align: center;font-size: 9pt; " width="100%" border="0" cellspacing="1" cellpadding="2">
<tbody>
<tr>
<td>هذه النسخة غير مرحلة
</td>
<td>Page {PAGENO} of {nbpg}
</td>
<td>' . $page_id . $rep_id . $master_id . '
</td>
</tr>
<tbody>
</table>
  </div>';


        $data = '<html>
<body>
<div>' . $headerdetail . '</div>
<p>' . $tableheader . '</p>
<p>' . $tablebody . '</p>
<p>' . $content_table . '</p>
</body>
</html>';

        $margin_header = $logo_height + 2;
        $margin_top = $logo_height + 40;

        $document = new PDF([
            'mode' => 'utf-8',
            'format' => $paper_type,
            // 'format' => [200, 250],
            'orientation' => $orientation,
            'margin_header' => $margin_header,
            'margin_top' => $margin_top,
            'margin_bottom' => '20',
            'margin_footer' => '2',
            'default_font_size' => $font_size,
            'default_font' => $font_name,
        ]);

        // return $table;


        $document->SetDirectionality('rtl');
        $document->SetHTMLHeader($headerhome);
        $document->SetHTMLFooter($headerfooter);
        $document->WriteHTML($data);


        $document->Output();
    }
}
