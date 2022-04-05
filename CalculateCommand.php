<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class CalculateCommand extends Command
{
    private $roman_symbols = ['I' => 1, 'V' => 5, 'X' => 10, 'L' => 50, 'C' => 100, 'D' => 500, 'M' => 1000];

    protected function configure()
    {
        $this->setName('calculate');
        $this->addArgument('expression');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expression = $input->getArgument('expression');
        if(!$this->validation($expression)){
            $output->writeln('<error>Error of expression</error>');
            return 0;
        }
        $action = '';
        if(!is_bool(strpos($expression, '+'))){
            $args = explode('+', $expression);
            $action = 'plus';
        }elseif (!is_bool(strpos($expression, '-'))){
            $args = explode('-', $expression);
            $action = 'minus';
        }
        foreach ($args as $key => $number){
            $args[$key] = $this->convertToArabic($number);
        }
        switch ($action){
            case 'plus':{
                $result = array_sum($args);
            }break;
            case 'minus':{
                $result = $args[0] - $args[1];
            }break;
        }
        $output->writeln($this->convertToRomain($result) . '(' . $result . ')');
        return 1;
    }

    private function convertToRomain($number){
        $number_elements = str_split((string)$number);

        $range = [
            ['I', 'V'],
            ['X', 'L'],
            ['C', 'D'],
            ['M', ''],
        ];
        $f = [
            [],
            [[0,1,0]],
            [[0,2,0]],
            [[0,3,0]],
            [[0,1,1],[0,1,0]],
            [[0,1,1]],
            [[0,1,0],[0,1,1]],
            [[0,2,0],[0,1,1]],
            [[0,3,0],[0,1,1]],
            [[1,1,0],[0,1,0]]
        ];

        $result = '';

        foreach (array_reverse($number_elements) as $key => $value){
            foreach ($f[$value] as $item) {
                $symbol = $range[$key + $item[0]][$item[2]];
                $result .= str_repeat($symbol, $item[1]);
            }
        }
        return ($number < 0 ? '-' : '') . implode('', array_reverse(str_split($result)));
    }

    private function convertToArabic($number){
        
        $number_elements = str_split($number);
        $result = 0;
        foreach ($number_elements as $key => $element){
            if(isset($number_elements[$key + 1])){
                if($this->roman_symbols[$element] < $this->roman_symbols[$number_elements[$key + 1]]){
                    $result -= $this->roman_symbols[$element];
                }else{
                    $result += $this->roman_symbols[$element];
                }
            }else {
                $result += $this->roman_symbols[$element];
            }
        }
        return $result;
    }

    private function validation($arg){
        
        if(!is_string($arg) || strlen($arg) == 0) return false;
        $arg = str_replace(' ', '', $arg);
        $arg = strtoupper($arg);

        if (preg_match('/[^(' . implode(',', array_keys($this->roman_symbols)). ',+,-]/', $arg)) return false;

        return true;
    }
}