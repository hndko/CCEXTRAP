<?php
class CC
{
    protected $bin;
    protected $check;
    protected $jml;

    public function __construct($bin, $check = 0, $jml = 0)
    {
        $this->bin = $bin;
        $this->check = $check;
        $this->jml = $jml;
    }

    private function color($color = "default", $text)
    {
        $arrayColor = array(
            'grey'         => '1;30',
            'red'         => '1;31',
            'green'     => '1;32',
            'yellow'     => '1;33',
            'blue'         => '1;34',
            'purple'     => '1;35',
            'nevy'         => '1;36',
            'white'     => '1;0',
        );
        return "\033[" . $arrayColor[$color] . "m" . $text . "\033[0m";
    }
    public function Execute()
    {
        echo "###############################################\n";
        echo "{~} Starting generation\n";
        echo "###############################################\n";
        sleep(5);
        if ($this->check < 1) {
            for ($i = 1; $i <= $this->jml; $i++) {
                echo $this->Extrap($this->bin) . "\n";
                sleep(1);
            }
        } else {
            for ($i = 1; $i <= $this->jml; $i++) {
                $card = $this->Extrap($this->bin);
                echo $this->Check($card) . "\n";
                sleep(1);
            }
        }
    }

    protected function generateYears()
    {
        $randMonth = rand(1, 12);
        $randYears = rand(20, 25);
        $randCvv = rand(010, 800);
        $randMonth < 10 ? $randMonth = "0" . $randMonth : $randMonth = $randMonth;
        $randCvv < 100 ? $randCvv = "0" . $randCvv : $randCvv = $randCvv;
        return "|" . $randMonth . "|20" . $randYears . "|" . $randCvv;
    }
    protected function Calculate($ccnumber, $length)
    {
        $sum = 0;
        $pos = 0;
        $reversedCCnumber = strrev($ccnumber);

        while ($pos < $length - 1) {
            $odd = $reversedCCnumber[$pos] * 2;
            if ($odd > 9) {
                $odd -= 9;
            }
            $sum += $odd;

            if ($pos != ($length - 2)) {

                $sum += $reversedCCnumber[$pos + 1];
            }
            $pos += 2;
        }

        # Calculate check digit
        $checkdigit = ((floor($sum / 10) + 1) * 10 - $sum) % 10;
        $ccnumber .= $checkdigit;
        return $ccnumber;
    }
    protected function Extrap($bin)
    {
        if (preg_match_all("#x#si", $bin)) {
            $ccNumber = $bin;
            while (strlen($ccNumber) < (16 - 1)) {
                $ccNumber .= rand(0, 9);
            }
            $ccNumber = str_split($ccNumber);
            $replace = "";
            foreach ($ccNumber as $cc => $key) {
                $replace .= str_replace("x", rand(0, 9), $key);
            }
            $Complete = $this->Calculate($replace, 16);
        } else {
            $ccNumber = $bin;
            while (strlen($ccNumber) < (16 - 1)) {
                $ccNumber .= rand(0, 9);
            }
            $Complete = $this->Calculate($ccNumber, 16);
        }
        return $Complete . $this->generateYears();
    }
    protected function Save($title, $text)
    {
        $fopen = fopen($title, "a");
        fwrite($fopen, $text);
        fclose($fopen);
    }
    protected function Check($card)
    {
        $headers = array();
        $headers[] = 'origin: https://uncoder.eu.org';
        $headers[] = 'accept-language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
        $headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'Accept: */*';
        $headers[] = 'referer: https://uncoder.eu.org/cc-checker/';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'Connection: keep-alive';
        $ch = curl_init();
        $options = array(
            CURLOPT_URL             => "https://uncoder.eu.org/cc-checker/api.php",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => "data=" . urlencode($card),
            CURLOPT_HTTPHEADER      => $headers
        );
        curl_setopt_array($ch, $options);
        $exec = curl_exec($ch);
        $status = json_decode($exec);
        switch ($status->error) {
            case '2':
                return $card . $this->color("red", " [ DIE ]");
                break;
            case '3':
                return $card . $this->color("grey", " [ UNKNOWN ]");
                break;
            case '4':
                return $card . $this->color("yellow", " [ CC NOT VALID ]");
                break;
            case '1':
                // $this->Save("Result-".$this->bin.".list", $card."\n");
                return $card . $this->color("green", " [ LIVE ]");
                break;
        }
    }
}
