<?php
namespace GFL\Tool;
use DB;
class Tool
{
    /**
     * Round Temperature
     *
     * @param [Float] $tem
     * @return void
     * @author [Phạm Tuấn Anh] [phamtuananh760@gmail.com]
     */
    public function roundTemperature($tem)
    {
        $tem = round($tem,2);
        $floor = floor($tem);
        $decimal = $tem - $floor;
        if (0.01 <= $decimal && $decimal <= 0.12) {
            return $floor;
        }
        if (0.13 <= $decimal && $decimal <= 0.37) {
            return $floor + 0.25;
        }
        if (0.38 <= $decimal && $decimal <= 0.62) {
            return $floor + 0.5;
        }
        if (0.63 <= $decimal && $decimal <= 0.87) {
            return $floor + 0.75;
        }
        if (0.88 <= $decimal && $decimal <= 0.99) {
            return $floor + 1;
        }
        return $floor;
    }
    /**
     * round VCF
     *
     * @param [float] $vcf
     * @return void
     * @author [Phạm Tuấn Anh] [phamtuananh760@gmail.com]
     */
    public function roundVCF($vcf)
    {
        return round($vcf, 4);
    }
    /**
     * round WCF
     *
     * @param [float] $wcf
     * @return void
     * @author [Phạm Tuấn Anh] [phamtuananh760@gmail.com]
     */
    public function roundWCF($wcf)
    {
        return round($wcf, 4);
    }
    public function roundL15($l15)
    {
        return round($l15, 4);
    }
    public function roundLit($lit)
    {
        return round($lit, 4);
    }
    public function roundKg($kg)
    {
        return round($kg, 4);
    }
    public function checkD15($dtt, $tem)
    {
        $data = [
            'D15' => '',
            'D151' => '',
            'D152' => '',
        ];
        $result = DB::table(config('tool.T53B.TABLE'))->select('*')->where(config('tool.T53B.TEMPERATURE'), $tem)->get();
        $d15 = $result->where(config('tool.T53B.DTT'), $dtt)->first();
        if ($d15) {
            $data['D15'] = $d15;
        } else {
            $data['D152'] = $result->where(config('tool.T53B.DTT'), '>', $dtt)->first();
            $data['D151'] = $result->where(config('tool.T53B.DTT'), '<', $dtt)->sortByDesc(config('tool.T53B.DTT'))->first();
        }
        return $data;
    }
    public function checkVCF($d15, $tem)
    {
        $data = [
            'VCF' => '',
            'VCF1' => '',
            'VCF2' => '',
        ];
        $result = DB::table(config('tool.T54B.TABLE'))->select('*')->where(config('tool.T54B.TEMPERATURE'), $tem)->get();
        $vcf = $result->where(config('tool.T54B.D15'), $d15)->first();
        if ($vcf) {
            $data['VCF'] = $vcf;
        } else {
            $data['VCF2'] = $result->where(config('tool.T54B.D15'), '>', $d15)->first();
            $data['VCF1'] = $result->where(config('tool.T54B.D15'), '<', $d15)->sortByDesc(config('tool.T54B.D15'))->first();
        }
        return $data;
    }
    public function checkWCF($d15)
    {
        $data = [
            'WCF' => '',
            'WCF1' => '',
            'WCF2' => '',
        ];
        $result = DB::table(config('tool.T56B.TABLE'))->select('*')->get();
        $wcf = $result->where(config('tool.T56B.D15'), $d15)->first();
        if ($wcf) {
            $data['WCF'] = $wcf->wcf;
        } else {
            $data['WCF2'] = $result->where(config('tool.T56B.D15'), '>', $d15)->first();
            $data['WCF1'] = $result->where(config('tool.T56B.D15'), '<', $d15)->sortByDesc(config('tool.T54B.D15'))->first();
        }
        return $data;
    }
    public function D15($d151, $d152, $dtt, $dtt1, $dtt2)
    {
        return $d151 + (($d152 - $d151) / ($dtt2 - $dtt1) * ($dtt - $dtt1));
    }
    public function VCF($vcf1, $vcf2, $d15, $d151, $d152)
    {
        return $vcf1 + (($vcf2 - $vcf1) / ($d152 - $d151) * ($d15 - $d151));
    }
    public function WCF($wcf1, $wcf2, $d15, $d151, $d152)
    {
        return $wcf1 + (($wcf2 - $wcf1) / ($d152 - $d151) * ($d15 - $d151));
    }
    public function gallonToLitre($gal)
    {
        return round($gal * 3.78541, 4);
    }
    public function calculateD15($dtt, $tem)
    {
        $column_d15 = config('tool.T53B.D15');
        $column_dtt = config('tool.T53B.DTT');
        $result = $this->checkD15($dtt, $tem);
        if ($result['D15'] != '') {
            return $result['D15']->$column_d15;
        } else {
            $d151 = $result['D151']->$column_d15;
            $d152 = $result['D152']->$column_d15;
            $dtt = $dtt;
            $dtt1 = $result['D151']->$column_dtt;
            $dtt2 = $result['D152']->$column_dtt;
            return $this->D15($d151, $d152, $dtt, $dtt1, $dtt2);
        }
    }
    public function calculateVCF($d15, $tem)
    {
        $column_vcf = config('tool.T54B.VCF');
        $column_d15 = config('tool.T53B.D15');
        $result = $this->checkVCF($d15, $tem);
        if ($result['VCF'] != '') {
            return $result['VCF']->$column_vcf;
        } else {
            $vcf1 = $result['VCF1']->$column_vcf;
            $vcf2 = $result['VCF2']->$column_vcf;
            $d15 = $d15;
            $d151 = $result['VCF1']->$column_d15;
            $d152 = $result['VCF2']->$column_d15;
            return $this->roundVCF($this->VCF($vcf1, $vcf2, $d15, $d151, $d152));
        }
    }
    public function calculateWCF($d15)
    {
        $column_wcf = config('tool.T56B.WCF');
        $column_d15 = config('tool.T56B.D15');
        $result = $this->checkWCF($d15);
        if ($result['WCF'] != '') {
            return $result['WCF']->$column_wcf;
        } else {
            $wcf1 = $result['WCF1']->$column_wcf;
            $wcf2 = $result['WCF2']->$column_wcf;
            $d15 = $d15;
            $d151 = $result['WCF1']->$column_d15;
            $d152 = $result['WCF2']->$column_d15;
            return $this->roundWCF($this->WCF($wcf1, $wcf2, $d15, $d151, $d152));
        }
    }
    public function convert($dtt, $tem, $gal, $unit_volumn, $exchange_rate, $price, $unit_price, $vat)
    {
        $data = [];
        $lit = strtolower($unit_volumn) == "gallon" ? $this->gallonToLitre($gal) : $gal;
        $tem = $this->roundTemperature($tem);
        $d15 = $this->calculateD15($dtt, $tem);
        $vcf = $this->calculateVCF($d15, $tem);
        $wcf = $this->calculateWCF($d15);
        // $vcf = $this->roundVCF($vcf);
        // $wcf = $this->roundWCF($wcf);
        // $lit = $this->roundLit($lit);
        $L15 = $lit * $vcf;
        $L15 = $this->roundL15($L15);
        $kg = $L15 * $wcf;
        $kg = $this->roundKg($kg);
        $subAmount = strtolower($unit_price) == "vnd/gallon" ? $kg * round($price, 4) : $kg * round($price, 4) * round($exchange_rate, 4);
        $vat = ($subAmount * round($vat, 4)) / 100;
        $amount = $subAmount + $vat;
        $data = [
            'D15' => $d15,
            'VCF' => $vcf,
            'WCF' => $wcf,
            'LIT' => $lit,
            'KG' => $kg,
            'sub_amount' => round($subAmount),
            'vat' => round($vat),
            'amount' => round($amount)
        ];
        return $data;
    }
}