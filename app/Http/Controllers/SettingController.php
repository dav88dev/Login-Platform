<?php

namespace App\Http\Controllers;

use App\Setting;
use DB;
use Illuminate\Http\Request;



/**
 * Class SettingController
 * @package App\Http\Controllers
 */
class SettingController extends Controller
{

    /**
     * Table rows count results
     *
     * @var array $table_counts
     */
    private $table_counts = array();

    /**
     * Utilisation settings
     *
     * @var array $utilisation_setting
     */
    private $utilisation_setting = array();

    /**
     * Get data by token
     *
     * @param string $token
     * @return mixed
     */
    public function getDataWithToken(string $token)
    {
        return ((new Setting())->where('setting->token', $token)->first());
    }

    /**
     * Return emails settings
     *
     * @return object
     */
    public function getEmailsSettings(): object
    {
        return Setting::where('type', 'email')->get();
    }

    /**
     * Return email settings by ID
     *
     * @param Request $request
     * @return object
     */
    public function getEmailsSettingsByID(Request $request): object
    {
        return Setting::where('id', $request->id)->get();
    }

    /**
     * Delete setting by ID
     *
     * @param Request $request
     */
    public function deleteSettingByID(Request $request): void
    {
        Setting::find($request->id)->delete();
    }

    /**
     * Create new email setting
     *
     * @param Request $request
     * @return int
     */
    public function newEmailsSettings(Request $request): int
    {
        $request->validate([
            'name' => 'required',
            'hotels' => 'required',
            'token' => 'required',
        ]);
        $setting = new Setting();
        $setting->type = 'email';
        $setting->setting = json_encode([
            'name' => $request->name,
            'token' => $request->token,
            'hotels' => json_encode($request->hotels)
        ]);
        $setting->save();
        return $setting->id;
    }

    /**
     * Update email setting
     *
     * @param Request $request
     * @return void
     */
    public function editEmailsSettings(Request $request): void
    {
        $request->validate([
            'name' => 'required',
            'hotels' => 'required',
            'token' => 'required',
        ]);
        Setting::where('id',
            $request->id)->update([
            'setting->name' => $request->name,
            'setting->token' => $request->token,
            'setting->hotels' => json_encode($request->hotels)
        ]);
    }

    /**
     * Register utilizations
     *
     * @return void
     */
    public function utilitiesRegister(): void
    {
        $this->utilizeDatabase();
    }

    /**
     * Database utilizations mechanism
     *
     * @return void
     */
    protected function utilizeDatabase(): void
    {
        $this->setUtilisationSettings();
        if ($this->utilisation_setting['on'] === false) {
            return;
        }
        $this->setCountTables();
        $this->deleteUnusefulRaws();
    }

    /**
     * Set utilisation settings
     *
     * @return void
     */
    protected function setUtilisationSettings(): void
    {
        $model = Setting::where('type', 'database_utilization')->first();
        $model = $model->setting;
        $this->utilisation_setting['percent'] = json_decode($model)->utilize;
        $this->utilisation_setting['on'] = json_decode($model)->on;
    }

    /**
     * Set count properties, count tables
     *
     * @return void
     */
    private function setCountTables(): void
    {
        $this->table_counts['radacct'] = DB::table('radacct')->where('nasporttype', 'Wireless-802.11')->count();
        $this->table_counts['client_auths'] = DB::table('client_auths')->count();
        $this->table_counts['radcheck'] = DB::table('radcheck')->where('router', 'no')->count();
        $this->table_counts['radpostauth'] = DB::table('radpostauth')->count();
    }

    /**
     * Delete old, not in use anymore data from DB
     *
     * @return void
     */
    private function deleteUnusefulRaws(): void
    {
        $limit = $this->getLimit($this->table_counts['radacct']);
        DB::table('radacct')->where('nasporttype', 'Wireless-802.11')->limit($limit)->delete();
        $limit = $this->getLimit($this->table_counts['client_auths']);
        DB::table('client_auths')->limit($limit)->delete();
        $limit = $this->getLimit($this->table_counts['radpostauth']);
        DB::table('radpostauth')->limit($limit)->delete();
        $limit = $this->getLimit($this->table_counts['radcheck']);
        if ($limit % 2 !== 0) {
            $limit += 1;
        }
        DB::table('radcheck')->where('router', 'no')->limit($limit)->delete();
        unset($limit);
    }

    /**
     * Get the percentage of rows to be deleted from tables
     *
     * @param int $number
     * @return int
     */
    private function getLimit(int $number): int
    {
        return (int)(($number * $this->utilisation_setting['percent']) / 100);
    }

    /**
     * Get utilisation settings
     *
     * @return array
     */
    public function getUtilisationSettings(): array
    {
        $this->setUtilisationSettings();
        return $this->utilisation_setting;
    }

    /**
     * Store utilisation settings in db
     *
     * @param Request $request
     * @return void
     */
    public function storeUtilisationSettings(Request $request): void
    {
        $request->validate([
            'on' => 'required|boolean',
            'percent' => 'required|integer',
        ]);
        Setting::where('type', 'database_utilization')->update([
            'setting->on' => $request->on,
            'setting->utilize' => (int)$request->percent
        ]);
    }

    /**
     * Reboot server or Freeradius server
     *
     * @param Request $request
     * @return void
     */
    public function serverManagement(Request $request):void
    {
        $request->validate([
            'action' => 'required|string',
        ]);
        if($request->action === 'reboot' ){
            exec('sudo /sbin/reboot');
        }
        if($request->action === 'freeradius' ){
            exec('sudo /etc/init.d/freeradius restart');
        }
    }

}
