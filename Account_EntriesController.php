<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRUDcontroller;
use App\Http\Controllers\FormController;
use App\Http\Controllers\TableController;
use App\Models\Account_Entries;
use App\Models\Seqment_cost_users;
use App\Models\Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Label;
use App\Models\Split_number;
use App\Models\AccountEntriesTab2;
use App\Models\Indexes_list_bills;
use App\Models\Currency;
use App\Models\entries_journal;


class Account_EntriesController extends Controller
{
    //
    public function AccountEntriesShow($id)
    {


        $pageId = session('pageId');
        $orgId = session('orgId');
        $tabId = 1;
        $css = '';

        $row = Account_Entries::FindOrFail($id);

        session()->put('showPage_' . $pageId . '-' . $tabId, $row);

        $colName = $row->company_name;
        $rowInfo = $this->getRowInfo($row, $colName);

        $setPage = $this->AccountEntriesPage()->setPage;


        ///////////// Table Page /////////////

        $tableHead = $this->tableHead;
        $tableBody = $this->AccountEntriesPage()->tableBody;


        $this->tablePage($tableHead, $tableBody);

        return $this->setView('account_entries.MainPage')->with(compact('rowInfo', 'row'));

    }

    public function AccountEntriesPage()
    {


        $pageId = 253;
        $tabId = 1;
        $css = '';
        $orgId = session('orgId');
        $folder = 'account_entries';
        $tabArray = array(
            'tab1' => ['lbl' => 3026, 'page' => $folder . '.tab1', 'page_id' => $pageId],
            'tab2' => ['lbl' => 2334, 'page' => $folder . '.tab2', 'class' => $css, 'page_id' => $pageId],
            //  'tabExtra'=>['lbl'=>extra_tabs(), 'class'=>$css],
        );

        $tabs = array_values($tabArray);

        $setPage = $this->setPage($pageId, $tabs, $tabId);

        ///////////// Table Page /////////////

        if (session('language') == 'rtl') {
            $currencyName2 = 'tc.cl_currency_name_ar as cl_currency_code';
        } else {
            $currencyName2 = 'tc.cl_currency_name as cl_currency_code';
        }

        $tableHead = $this->tableHead;
        $tableBody = DB::table('tb_account_entries_first as taef')
            ->select('taef.id as control', 'taef.id as id', 'taef.serial_id as txtid1',
                $currencyName2, 'taef.coin_price_tree as coin_price_tree', 'taef.entry_notes as txtnotes',
                'taef.reference_number as txtreference_number', 'taef.entry_date as entry_date',
                'taef.sum_credit as trial_credit', 'taef.sum_debit as trial_debit',
                'taef.sum_debit_tree as tree_debit', 'taef.sum_credit_tree as tree_credit',
                'taef.visible as txtid2', $currencyName2, 'taef.entry_notes as entry_notes')
            // ->addSelect(DB::raw("'delete' del_but" ))

            ->join('tb_currency as tc', function ($join) {
                $join->on('taef.cl_currency_code', 'tc.cl_currency_code')
                    ->where('taef.branch_id', '=', session('orgId'))
                    ->where('taef.cl_organization_id', '=', session('orgHead'))
                    ->where('taef.visible', '=', '1');
            })->get();

        $this->tablePage($tableHead, $tableBody);

        return $this->setView('account_entries.MainPage');

    }

    public function AccountEntriesStore(Request $request)
    {


        $pageId = session('pageId');
        $auto_note_arr = '';

        $array = [
            'tb_name' => 'tb_account_entries_first',
            'return_sql' => true,
            'hasSerial' => 'yes',
            'sql' => [
                'page_id' => $pageId,
                'auto_notes' => $auto_note_arr,
                'cl_organization_id' => session('orgHead'),
                'branch_id' => session('orgId'),
            ],
        ];


        $CRUD = new CRUDcontroller();
        $InsertId = $CRUD->Create($array, $request);


    }

    public function AccountEntriesUpdate(Request $request)
    {

        $id = $request->id;

        $req = json_encode($request->formData);
        $req = json_decode($req);

        foreach ($req as $key => $row) {


            if (!isset($row->name)) {
                $row->name = $key;
            }

            if ($row->name == 'entry_date') {

                $entry_date = $row->value;

            }


        }

        $CRUD = new CRUDcontroller();

        $array = [
            'tb_name' => 'tb_account_entries_first',
            'return_sql' => true,
            'id' => 'id=' . $id,
            'sql' => [
                "entry_date" => $entry_date,
            ],
            'notIn' => ['serial_id', 'cl_currency_code', 'coin_price_tree', 'reference_number', 'entry_notes'],
        ];

        $array2 = [
            'tb_name' => 'tb_acc_entry_details_first',
            'return_sql' => true,
            'id' => 'entry_number=' . $id,
            'sql' => [
                "entry_date" => $entry_date,
            ],
        ];
        $UpdateId = $CRUD->Update($array, $request);
        $request1 = '';
        $Updatedate = $CRUD->Update($array2, $request1);

    }

    public function AccountEntriesDelete(Request $request)
    {
        $id = $request->id;
        $CRUD = new CRUDcontroller();

        $array = [
            'tb_name' => 'tb_account_entries_first',
            'id' => 'id=' . $id,
        ];

        $DeleteId = $CRUD->Delete($array);

    }


    ///------Tab2 -----////
    public function AccountEntriesTab2Page($id)
    {


        $pageId = 253;
        $tabId = '2';

        $orgId = session('orgId');
        $css = '';

        $row = Account_Entries::find($id);

        $colName = $row->cl_organization_name;
        $rowInfo = $this->getRowInfo($row, $colName);
        $rowid = reset($rowInfo);

        $tabId = '2';
        $folder = 'account_entries';

        $this->tabArray = array(
            'tab1' => ['lbl' => 3026, 'page' => $folder . '.tab1', 'page_id' => $pageId],
            'tab2' => ['lbl' => 2334, 'page' => $folder . '.tab2', 'class' => $css, 'page_id' => $pageId],

        );

        $tabs = array_values($this->tabArray);

        $this->setPage($pageId, $tabs, $tabId);
        ///////////// Table Page /////////////

        $id_org = $row->cl_organization_id;
        session()->put('id_org', $id_org);

        $tableHead = $this->tableHead;


        $tableBody = DB::table('tb_acc_entry_details_first as taedf')
            ->select('taedf.id as control', 'taedf.entry_number as control1', 'taedf.complete_account_numebr as txtaccnumber', 'tat.cl_ar_real_name as cl_ar_real_name',
                'taedf.account_number as account_number', 'taedf.debit_rec as debit_rec',
                'taedf.credit_rec as credit_rec',
                'taedf.coin_price_acc as coin_price_acc', 'tc.cl_currency_name_ar as account_currency',
                'taedf.account_detail as account_detail', 'taedf.complete_account_numebr as txtid2', 'taedf.debit_acc as debit_acc',
                'taedf.debit_acc as debit_acc', 'taedf.credit_acc as credit_acc',
                'taedf.accountamount as accountamount',
                'taedf.sub_branch_id as sub_branch_id',
                'taedf.index_type as index_type', 'index_name_id as index_name_id'
            )
            //->addSelect(DB::raw("'Confirm' lblFix_btn2" ))

            ->join('tb_account_tree as tat', 'taedf.account_number', 'tat.account_number')
            ->join('tb_currency as tc', 'taedf.account_currency', 'tc.cl_currency_code')
            ->where('taedf.entry_number', $rowid)
            ->where('taedf.branch_no', session('orgId'))
            ->where('tat.cl_organization_id', session('orgHead'))
            ->get();

        $this->tablePage($tableHead, $tableBody);


        // SELECT trcd.id ,tsc.id,tsc.ar_name,tsc.en_name,tsc.parent_id
        // FROM `tb_role1_cost_details` trcd
        // join tb_role1_cost_master trcm on trcm.id = trcd.master_id
        // join tb_seqment_cost tsc on tsc.id = trcd.`account_id`
        // join tb_seqment_cost_user tscu on tscu.seq_cost_id = trcm.id and tscu.Cl_User_name = 'aaa' and tsc.Cl_Organization_id = 1 and tsc.visible = 1
        // WHERE 1

        $comp = Seqment_cost_users::selectRaw("DISTINCT id")->selectRaw("concat(ar_name,' ( ',length,' )') as ar_name")
            ->selectRaw('cost_center_initial_value')
            ->where('parent_id', '=', 0)
            ->where('cl_user_name', session('userName'))
            ->where('cl_organization_id', session('orgHead'))
            ->where('is_enabled', '33')->get();


        return $this->setView('account_entries.tab2')->with(compact('row', 'rowInfo', 'rowid', 'comp'));


    }


    public function AccountEntriesShowtab2($id, $subId)
    {


        $row = AccountEntriesTab2::find($subId);
        $pageId = 253;
        $orgId = session('orgId');
        $tabId = 2;
        $css = '';
        $rowid = $row->entry_number;


        session()->put('showPage_' . $pageId . '-' . $tabId, $row);


        $colName = $row->id;
        $rowInfo = $this->getRowInfo($row, $colName);

        $setPage = $this->setPage($pageId, $tabs = [], $tabId);
        ///////////// Table Page /////////////

        $tableHead = $this->tableHead;

        $tableBody = DB::table('tb_acc_entry_details_first as taedf')
            ->select('taedf.id as control', 'taedf.entry_number as control1', 'taedf.entry_number as control9', 'taedf.complete_account_numebr as txtaccnumber', 'tat.cl_ar_real_name as cl_ar_real_name',
                'taedf.account_number as account_number', 'taedf.debit_rec as debit_rec',
                'taedf.credit_rec as credit_rec',
                'taedf.coin_price_acc as coin_price_acc', 'tc.cl_currency_name_ar as account_currency',
                'taedf.account_detail as account_detail', 'taedf.complete_account_numebr as txtid2', 'taedf.debit_acc as debit_acc',
                'taedf.debit_acc as debit_acc', 'taedf.credit_acc as credit_acc',
                'taedf.accountamount as accountamount',
                'taedf.sub_branch_id as sub_branch_id',

                'taedf.index_type as index_type', 'index_name_id as index_name_id'
            )
            ->join('tb_account_tree as tat', 'taedf.account_number', 'tat.account_number')
            ->join('tb_currency as tc', 'taedf.account_currency', 'tc.cl_currency_code')
            ->where('taedf.entry_number', $rowid)
            ->where('taedf.branch_no', session('orgId'))
            ->where('tat.cl_organization_id', session('orgHead'))
            ->get();

        $this->tablePage($tableHead, $tableBody);


        $comp = Seqment_cost_users::selectRaw("DISTINCT id")->selectRaw("concat(ar_name,' ( ',length,' )') as ar_name")
            ->selectRaw('cost_center_initial_value')
            ->where('parent_id', '=', 0)
            ->where('cl_user_name', session('userName'))
            ->where('cl_organization_id', session('orgHead'))
            ->where('is_enabled', '33')->get();

        $id = $subId;

        return $this->setView('account_entries.tab2')->with(compact('rowInfo', 'row', 'comp', 'rowid', 'id'));

    }


    public function AccountEntriesTab2Store($id, Request $requestArray)
    {

        $id = $requestArray->id;
        $request = json_encode($requestArray->formData);
        $request = json_decode($request);
        $pageId = session('pageId');
        $auto_note_arr = '';

        if (session('language') == 'rtl') {
            $currencyname = 'cl_currency_name_ar';

        } else {
            $currencyname = 'cl_currency_name';

        }
        foreach ($request as $key => $row) {

            if (!isset($row->name)) {
                $row->name = $key;
            }

            if ($row->name == 'account_currency') {
                $account_currency = $row->value;
            }
            if ($row->name == 'index_type') {
                $index_type = $row->value;
            }

            if ($row->name == 'complete_account_numebr') {
                $complete_account_numebr = $row->value;
            }
            if ($row->name == 'debit_rec') {
                $debit_rec = $row->value;
            }
            if ($row->name == 'credit_rec') {
                $credit_rec = $row->value;
            }

            if ($row->name == 'index_name_id') {
                $index_name_id = $row->value;
            }

            if ($row->name == 'entry_notes') {
                $entry_notes = $row->value;
            }

            if ($row->name == 'index_type') {
                $index_type = $row->value;
            }

            if ($row->name == 'accounts_types') {
                $accounts_types = $row->value;
            }

            if ($row->name == 'site_id') {
                $site_id = $row->value;

                if (!empty($site_id)) {
                    $site = $site_id;
                } else {
                    $site = '0';
                }
            }
        }


        $account_currency = DB::table('tb_currency')->select('cl_currency_code')->where($currencyname, $account_currency)->first();
        $account_currency = $account_currency->cl_currency_code;


        $account_number = $complete_account_numebr;

        //dd($account_number);
        $account_number1 = Split_number::SplitNumber($account_number, 'last');
        $account_number2 = Split_number::SplitNumber($account_number, 'first');
        $account_first = DB::table('tb_account_entries_first')->where('id', $id)->first();
        $entry_date = $account_first->entry_date;
        $cl_currency_code = $account_first->cl_currency_code;
        $coin_price_tree = $account_first->coin_price_tree;
        $auto_notes = $account_first->entry_notes;

        $account_tree = DB::table('tb_account_tree')->where('account_number', $account_number1)->first();
        $account_number_levels = $account_tree->account_number_levels;

        $number = explode('.', $account_number_levels);
        $count = count($number);


        $arr = [
            'page_id' => $pageId,
            'cl_organization_id' => session('orgHead'),
            'branch_no' => session('orgId'),
            'entry_number' => $id,
            'account_number' => $account_number1,
            'account_currency' => $account_currency,
            'entry_date' => $entry_date,
            'cl_currency_code' => $cl_currency_code,
            'coin_price_tree' => $coin_price_tree,
            'auto_notes' => $auto_notes,
        ];

        $level_ = 'level_';

// indexes_accounts_types_tb
// indexes_linked_accounts_types_tb
        /*SELECT 1 IdxSer, iatt.id TxtId, iatt.account_number Txbnum,ilat.ar_name TxtName,iatt.index_id TxtId2 from indexes_accounts_types_tb iatt ,indexes_linked_accounts_types_tb ilat where iatt.index_type = 240 and iatt.index_id = '10' and iatt.account_type_id = ilat.id */


        $value1 = '';
        $tmpArray = array();
        foreach ($number as $key => $value) {
            $value1 = $value1 . $value;
            $tmpArray[$level_ . $key] = $value1;

        }
        for ($i = $count; $i <= 10; $i++) {
            $tmpArray[$level_ . $i] = $account_number1;
        }


        $accountnumber_seg = explode('.', $account_number2);
        $count_seg = count($number);
        $newlevel = '';
        $seg = 'seg_cost';
        $segArray = array();
        foreach ($accountnumber_seg as $key => $value) {
            $key = $key + 1;
            $newlevel = $newlevel . $value;
            $segArray[$seg . $key] = $newlevel;

        }

        $arr = array_merge($arr, $tmpArray, $segArray);


        $array = [
            'tb_name' => 'tb_acc_entry_details_first',
            'return_sql' => true,
            'sql' => $arr,
        ];

        // dd($array);
        $balance = $debit_rec + $credit_rec;


        if ($index_type == '240') {
            $cl_currency = DB::table('tb_osraty_emp')
                ->where('cl_employee_id', '=', $index_name_id)
                ->where('is_enabled', '=', '33')
                ->where('visible', '=', '1')
                ->pluck('cl_currency_code')
                ->first();
            $headorg = session('orgHead');
            $currency_index = Currency::exchange($cl_currency_code, $cl_currency, $headorg);


        }
        if ($index_type == '241') {
            $cl_currency = DB::table('customer_sale')
                ->where('cust_id', '=', $index_name_id)
                ->where('is_enabled', '=', '33')
                ->where('visible', '=', '1')
                ->pluck('cl_currency_code')
                ->first();
            $headorg = session('orgHead');
            $currency_index = Currency::exchange($cl_currency_code, $cl_currency, $headorg);

        }
        if ($index_type == '242') {
            $cl_currency = DB::table('tb_supplier')
                ->where('cl_id', '=', $index_name_id)
                ->where('is_enabled', '=', '33')
                ->where('visible', '=', '1')
                ->pluck('cl_currency_code')
                ->first();
            $headorg = session('orgHead');
            $currency_index = Currency::exchange($cl_currency_code, $cl_currency, $headorg);
        }

        $CRUD = new CRUDcontroller();

        $request1 = $this->getRequest($requestArray);

        $InsertId = $CRUD->Create($array, $request1);

        $lastid = DB::table('tb_acc_entry_details_first')->pluck('id')->last();

        $lastid = $lastid;


        if (!empty($index_type)) {

            $array1 = [
                'page_id' => $pageId,
                'cl_organization_id' => session('orgHead'),
                'branch_no' => session('orgId'),
                'group_id' => session('groupId'),
                'dateentry' => $entry_date,
                'debit' => $debit_rec,
                'credit' => $credit_rec,
                'cl_currency_code' => $cl_currency_code,
                'coin_price_tree' => $coin_price_tree,
                'entry_resource' => 1,
                'visible' => 1,
                'auto_notes' => $auto_notes,
                'details_id' => $lastid,
                'index_type' => $index_type,
                'index_name_id' => $index_name_id,
                'balance' => $balance,
                'page_id_to' => $pageId,
                'details_id_to' => $lastid,
                'site_id' => $site,
                'accounts_types' => $accounts_types,
            ];
            //dd($balance);

            $Insertindex = Indexes_list_bills::insert_bill($array1);

        }


    }


///update_tab2
    public function AccountEntriesUpdateTab2($id, $subId, Request $request)
    {

        $pageId = '253';
        $id = $subId;

        //dd($request->all());

        $req = json_encode($request->formData);
        $req = json_decode($req);
// print_r($req);


        foreach ($req as $key => $row) {


            if (!isset($row->name)) {
                $row->name = $key;
            }

            if ($row->name == 'complete_account_numebr') {

                $account_number = $row->value;

            }

            if ($row->name == 'account_currency') {

                $account_currency = $row->value;

            }
            if ($row->name == 'debit_rec') {

                $debit_rec = $row->value;

            }
            if ($row->name == 'credit_rec') {

                $credit_rec = $row->value;

            }

            if ($row->name == 'site_id') {

                $site_id = $row->value;

            }

            if ($row->name == 'account_detail') {

                $entry_notes = $row->value;

            }

            if ($row->name == 'index_type') {

                $index_type = $row->value;

            }
            if ($row->name == 'index_name_id') {

                $index_name_id = $row->value;

            }
            if ($row->name == 'coin_price_acc') {

                $coin_price_acc = $row->value;

            }

            if ($row->name == 'account_price_Headorg') {

                $account_price_Headorg = $row->value;

            }

            if ($row->name == 'accounts_types') {

                $accounts_types = $row->value;

            }

        }
        //dd($account_currency);

        $CRUD = new CRUDcontroller();

        if (session('language') == 'rtl') {
            $currencyname = 'cl_currency_name_ar';

        } else {
            $currencyname = 'cl_currency_name';

        }

        $account_currency = DB::table('tb_currency')->select('cl_currency_code')->where($currencyname, $account_currency)->first();
        $account_currency = $account_currency->cl_currency_code;


        $account_number1 = Split_number::SplitNumber($account_number, 'last');
        $account_number2 = Split_number::SplitNumber($account_number, 'first');


        $row_detail = DB::table('tb_acc_entry_details_first')->where('id', $id)->first();


        $entry_number = $row_detail->entry_number;

        $account_first = DB::table('tb_account_entries_first')->where('id', $entry_number)->first();
        $entry_date = $account_first->entry_date;
        $cl_currency_code = $account_first->cl_currency_code;
        $coin_price_tree = $account_first->coin_price_tree;

        $account_tree = DB::table('tb_account_tree')->where('account_number', $account_number1)->first();
        $account_number_levels = $account_tree->account_number_levels;

        $number = explode('.', $account_number_levels);
        $count = count($number);


        $arr = [

            'entry_number' => $entry_number,
            'account_number' => $account_number1,
            'account_currency' => $account_currency,
            'cl_currency_code' => $cl_currency_code,
            'coin_price_tree' => $coin_price_tree,
        ];

        $level_ = 'level_';


        $value1 = '';
        $tmpArray = array();
        foreach ($number as $key => $value) {
            $value1 = $value1 . $value;
            $tmpArray[$level_ . $key] = $value1;

        }
        for ($i = $count; $i <= 10; $i++) {
            $tmpArray[$level_ . $i] = $account_number1;
        }


        $accountnumber_seg = explode('.', $account_number2);
        $count_seg = count($number);
        $newlevel = '';
        $seg = 'seg_cost';
        $segArray = array();
        foreach ($accountnumber_seg as $key => $value) {
            $key = $key + 1;
            $newlevel = $newlevel . $value;
            $segArray[$seg . $key] = $newlevel;

        }

        $arr = array_merge($arr, $tmpArray, $segArray);


        $array = [
            'tb_name' => 'tb_acc_entry_details_first',
            'return_sql' => true,
            'id' => 'id=' . $id,
            'sql' => $arr,
        ];


        $UpdateId = $CRUD->Update($array, $request);

        $balance = $debit_rec + $credit_rec;
        if (!empty($site_id)) {
            $site = $site_id;
        } else {
            $site = '0';
        }
        if (!empty($accounts_types)) {
            $accounts_types = $accounts_types;
        } else {
            $accounts_types = '0';
        }

        if ($index_type == '240') {
            $cl_currency = DB::table('tb_osraty_emp')
                ->where('cl_employee_id', '=', $index_name_id)
                ->where('is_enabled', '=', '33')
                ->where('visible', '=', '1')
                ->pluck('cl_currency_code')
                ->first();
            $headorg = session('orgHead');
            $currency_index = Currency::exchange($cl_currency_code, $cl_currency, $headorg);


        }
        if ($index_type == '241') {
            $cl_currency = DB::table('customer_sale')
                ->where('cust_id', '=', $index_name_id)
                ->where('is_enabled', '=', '33')
                ->where('visible', '=', '1')
                ->pluck('cl_currency_code')
                ->first();
            $headorg = session('orgHead');
            $currency_index = Currency::exchange($cl_currency_code, $cl_currency, $headorg);

        }
        if ($index_type == '242') {
            $cl_currency = DB::table('tb_supplier')
                ->where('cl_id', '=', $index_name_id)
                ->where('is_enabled', '=', '33')
                ->where('visible', '=', '1')
                ->pluck('cl_currency_code')
                ->first();
            $headorg = session('orgHead');
            $currency_index = Currency::exchange($cl_currency_code, $cl_currency, $headorg);

        }
        $accountid = Indexes_list_bills::where('page_id_to', '253')->where('details_id_to', $id)->pluck('id')->first();
        if (!empty($accountid)) {

            if (!empty($index_type)) {

                $idupdate = 'page_id_to=' . $pageId . ' and details_id_to=' . $id;
                $array1 = [

                    'cl_organization_id' => session('orgHead'),
                    'branch_no' => session('orgId'),
                    'group_id' => session('groupId'),
                    'dateentry' => $entry_date,
                    'debit' => $debit_rec,
                    'credit' => $credit_rec,
                    'cl_currency_code' => $cl_currency_code,
                    'coin_price_tree' => $coin_price_tree,
                    'entry_resource' => 1,
                    'visible' => 1,
                    'auto_notes' => $entry_notes,
                    'index_type' => $index_type,
                    'index_name_id' => $index_name_id,
                    'balance' => $balance,
                    'site_id' => $site,
                    'accounts_types' => $accounts_types,
                ];

                $Insertindex = Indexes_list_bills::update_bill($array1, $idupdate, $id);
            }
        } else {

            if (!empty($index_type)) {
                $array1 = [
                    'page_id' => $pageId,
                    'cl_organization_id' => session('orgHead'),
                    'branch_no' => session('orgId'),
                    'group_id' => session('groupId'),
                    'dateentry' => $entry_date,
                    'debit' => $debit_rec,
                    'credit' => $credit_rec,
                    'cl_currency_code' => $cl_currency_code,
                    'coin_price_tree' => $coin_price_tree,
                    'entry_resource' => 1,
                    'visible' => 1,
                    'auto_notes' => $entry_notes,
                    'details_id' => $id,
                    'index_type' => $index_type,
                    'index_name_id' => $index_name_id,
                    'balance' => $balance,
                    'site_id' => $site,
                    'page_id_to' => $pageId,
                    'details_id_to' => $id,
                    'accounts_types' => $accounts_types,


                ];
                $Insertindex = Indexes_list_bills::insert_bill($array1);

            }

        }


    }


    public function AccountEntriesConfirmTab2(Request $request)
    {


        $id = $request->rowid;
        $all_row = DB::table('tb_acc_entry_details_first')->select('id')->where('entry_number', $id)->where('visible', '1')->get();
        $account_c_d = DB::table('tb_acc_entry_details_first')->select('id', 'entry_date', 'credit_rec', 'debit_rec', 'auto_notes', 'complete_account_numebr', 'index_type', 'index_name_id', 'sub_branch_id', 'accounts_types', 'site_id')->where('entry_number', $id)->where('visible', '1')->get();


        foreach ($account_c_d as $row) {

            $acc[] = [
                'id' => $row->id,
                'entry_date' => $row->entry_date,
                'credit_rec' => $row->credit_rec,
                'debit_rec' => $row->debit_rec,
                'auto_notes' => $row->auto_notes,
                'complete_account_numebr' => $row->complete_account_numebr,
                'index_type' => $row->index_type,
                'index_name_id' => $row->index_name_id,
                'sub_branch_id' => $row->sub_branch_id,
                'accounts_types' => $row->accounts_types,
                'site_id' => $row->site_id,

            ];

        }


        $parent_row = DB::table('tb_account_entries_first')->where('id', $id)->where('visible', '1')->first();
        $arraccount = '';
        $array = [
            'id' => $id,
            'cl_organization_id' => $parent_row->cl_organization_id,
            'branch_no' => $parent_row->branch_id,
            'group_id' => $parent_row->group_id,
            'page_id' => $parent_row->page_id,
            'details_id' => $parent_row->id,
            'entry_date' => $parent_row->entry_date,
            'tree_currency' => session('curOrg'),
            'auto_notes' => $parent_row->entry_notes,
            'page_id_to' => $parent_row->page_id,
            'details_id_to' => $parent_row->id,
            'url' => 'account_entries/tab2',
            'cl_currency_code' => $parent_row->cl_currency_code,
            'coin_price_tree' => $parent_row->coin_price_tree,
            'entry_resource' => $parent_row->entry_resource,

        ];


        foreach ($all_row as $row) {
            $accountid[] = Indexes_list_bills::where('page_id_to', '253')->where('details_id_to', $row->id)->pluck('id')->first();

        }


        $sumcredit_rec = DB::table('tb_acc_entry_details_first')->where('entry_number', $id)->sum('credit_rec');
        $sumdebit_rec = DB::table('tb_acc_entry_details_first')->where('entry_number', $id)->sum('debit_rec');

        $sumcredit_acc = DB::table('tb_acc_entry_details_first')->where('entry_number', $id)->sum('credit_acc');
        $sumdebit_acc = DB::table('tb_acc_entry_details_first')->where('entry_number', $id)->sum('debit_acc');


        // if($sumcredit_rec==$sumdebit_rec){
        //    $update=  DB::table('tb_account_entries_first')
        //   ->where('id', $id)
        //   ->where('visible','1')
        //   ->update(['sum_debit' => $sumdebit_rec,'sum_credit' => $sumcredit_rec,'sum_debit_tree' => $sumdebit_acc,'sum_credit_tree' => $sumcredit_acc,'visible' =>'2']);

        //   $update2=  DB::table('tb_acc_entry_details_first')
        //   ->where('entry_number', $id)
        //   ->where('visible','1')
        //   ->update(['visible' =>'2']);


        //   foreach($accountid as $bill){
        //     $update2=  DB::table('indexes_list_bills')
        //     ->where('id', $bill)
        //     ->where('visible','1')
        //     ->update(['visible' =>'2']);
        // }

        // $entries_journal=entries_journal::entries_journal($array,$acc);


        if ($sumcredit_rec == $sumdebit_rec) {

            $entries_journal = entries_journal::entries_journal($array, $acc);

            $request = '';

            $updatearrayaccount = [
                'tb_name' => 'tb_account_entries_first',
                'return_sql' => true,
                'id' => 'id=' . $id,
                'conditions' => 'id=' . $id . ' and visible=1',
                'sql' => [
                    'visible' => '2',
                    'sum_debit' => $sumdebit_rec,
                    'sum_credit' => $sumcredit_rec,
                    'sum_debit_tree' => $sumdebit_acc,
                    'sum_credit_tree' => $sumcredit_acc,

                ],
            ];
            $CRUD = new CRUDcontroller();


            $update2 = $CRUD->Update($updatearrayaccount, $request);

            $updatearrayacc_entry = [
                'tb_name' => 'tb_acc_entry_details_first',
                'return_sql' => true,
                'id' => 'id=' . $id,
                'conditions' => 'entry_number=' . $id . ' and visible=1',
                'sql' => [
                    'visible' => '2',
                ],
            ];
            $CRUD = new CRUDcontroller();


            $update2 = $CRUD->Update($updatearrayacc_entry, $request);


            if (isset($accountid)) {

                foreach ($accountid as $bill) {
                    if ($bill != 0) {
                        $request = '';
                        $updatearray = [
                            'tb_name' => 'indexes_list_bills',
                            'return_sql' => true,
                            'id' => 'id=' . $bill,
                            'sql' => [
                                'visible' => '2',
                            ],
                        ];
                        $CRUD = new CRUDcontroller();


                        $update2 = $CRUD->Update($updatearray, $request);
                    }
                }
            }

            return 'true';
        } else {
            return 'false';
        }


    }


}
