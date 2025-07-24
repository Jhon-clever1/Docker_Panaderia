<?php
class Apriori {
    private $minSup;
    private $minConf;
    private $maxScan;
    private $delimiter;
    private $transactions;
    private $rules;
    
    public function __construct() {
        $this->minSup = 0.1;
        $this->minConf = 0.5;
        $this->maxScan = 20;
        $this->delimiter = ',';
        $this->transactions = array();
        $this->rules = array();
    }
    
    public function setMaxScan($maxScan) {
        $this->maxScan = $maxScan;
    }
    
    public function setMinSup($minSup) {
        $this->minSup = $minSup;
    }
    
    public function setMinConf($minConf) {
        $this->minConf = $minConf;
    }
    
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }
    
    public function process($transactions) {
        $this->transactions = $transactions;
        $itemsets = $this->findFrequentItemsets();
        $this->rules = $this->generateRules($itemsets);
    }
    
    public function getRules() {
        return $this->rules;
    }
    
    private function findFrequentItemsets() {
        $itemsets = array();
        $frequentItemsets = array();
        
        // Paso 1: Encontrar itemsets frecuentes de tamaño 1
        $c1 = array();
        foreach($this->transactions as $transaction) {
            foreach($transaction as $item) {
                if(!isset($c1[$item])) {
                    $c1[$item] = 0;
                }
                $c1[$item]++;
            }
        }
        
        $l1 = array();
        foreach($c1 as $item => $count) {
            $support = $count / count($this->transactions);
            if($support >= $this->minSup) {
                $l1[] = array($item);
                $itemsets[implode($this->delimiter, array($item))] = $support;
            }
        }
        
        $k = 2;
        $lk = $l1;
        
        // Pasos siguientes: Encontrar itemsets frecuentes de tamaño k
        while($k <= $this->maxScan && count($lk) > 0) {
            $ck = $this->aprioriGen($lk, $k);
            
            $counts = array();
            foreach($ck as $itemset) {
                $key = implode($this->delimiter, $itemset);
                $counts[$key] = 0;
                
                foreach($this->transactions as $transaction) {
                    if($this->containsAll($transaction, $itemset)) {
                        $counts[$key]++;
                    }
                }
            }
            
            $lk = array();
            foreach($counts as $itemsetStr => $count) {
                $support = $count / count($this->transactions);
                if($support >= $this->minSup) {
                    $itemset = explode($this->delimiter, $itemsetStr);
                    $lk[] = $itemset;
                    $itemsets[$itemsetStr] = $support;
                }
            }
            
            $k++;
        }
        
        return $itemsets;
    }
    
    private function aprioriGen($lk, $k) {
        $ck = array();
        $len = count($lk);
        
        for($i = 0; $i < $len; $i++) {
            for($j = $i+1; $j < $len; $j++) {
                $itemset1 = $lk[$i];
                $itemset2 = $lk[$j];
                
                $merged = array_unique(array_merge($itemset1, $itemset2));
                sort($merged);
                
                if(count($merged) == $k && !$this->hasInfrequentSubset($merged, $lk)) {
                    $ck[] = $merged;
                }
            }
        }
        
        return $ck;
    }
    
    private function hasInfrequentSubset($itemset, $lk) {
        $subsets = $this->getSubsets($itemset, count($itemset)-1);
        
        foreach($subsets as $subset) {
            $found = false;
            foreach($lk as $frequent) {
                if($this->arraysEqual($subset, $frequent)) {
                    $found = true;
                    break;
                }
            }
            
            if(!$found) {
                return true;
            }
        }
        
        return false;
    }
    
    private function getSubsets($itemset, $k) {
        $subsets = array();
        $n = count($itemset);
        $indices = range(0, $n-1);
        $combinations = $this->combinations($indices, $k);
        
        foreach($combinations as $combination) {
            $subset = array();
            foreach($combination as $index) {
                $subset[] = $itemset[$index];
            }
            $subsets[] = $subset;
        }
        
        return $subsets;
    }
    
    private function combinations($items, $k) {
        if($k == 0) {
            return array(array());
        }
        
        if(count($items) == 0) {
            return array();
        }
        
        $first = $items[0];
        $rest = array_slice($items, 1);
        
        $combinationsWithoutFirst = $this->combinations($rest, $k);
        $combinationsWithFirst = $this->combinations($rest, $k-1);
        
        foreach($combinationsWithFirst as &$combination) {
            array_unshift($combination, $first);
        }
        
        return array_merge($combinationsWithFirst, $combinationsWithoutFirst);
    }
    
    private function arraysEqual($a, $b) {
        if(count($a) != count($b)) {
            return false;
        }
        
        sort($a);
        sort($b);
        
        return $a === $b;
    }
    
    private function containsAll($transaction, $itemset) {
        foreach($itemset as $item) {
            if(!in_array($item, $transaction)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function generateRules($itemsets) {
        $rules = array();
        
        foreach($itemsets as $itemsetStr => $support) {
            $itemset = explode($this->delimiter, $itemsetStr);
            if(count($itemset) < 2) continue;
            
            $subsets = $this->getAllSubsets($itemset);
            
            foreach($subsets as $antecedent) {
                $consequent = array_diff($itemset, $antecedent);
                if(count($consequent) == 0) continue;
                
                $antecedentStr = implode($this->delimiter, $antecedent);
                $consequentStr = implode($this->delimiter, $consequent);
                
                if(isset($itemsets[$antecedentStr])) {
                    $confidence = $support / $itemsets[$antecedentStr];
                    
                    if($confidence >= $this->minConf) {
                        $lift = $support / ($itemsets[$antecedentStr] * $itemsets[$consequentStr]);
                        
                        $rules[] = array(
                            'antecedent' => $antecedent,
                            'consequent' => $consequent,
                            'support' => $support,
                            'confidence' => $confidence,
                            'lift' => $lift
                        );
                    }
                }
            }
        }
        
        // Ordenar reglas por lift descendente
        usort($rules, function($a, $b) {
            return $b['lift'] <=> $a['lift'];
        });
        
        return $rules;
    }
    
    private function getAllSubsets($itemset) {
        $subsets = array();
        $n = count($itemset);
        
        for($k = 1; $k < $n; $k++) {
            $combinations = $this->combinations(range(0, $n-1), $k);
            
            foreach($combinations as $combination) {
                $subset = array();
                foreach($combination as $index) {
                    $subset[] = $itemset[$index];
                }
                $subsets[] = $subset;
            }
        }
        
        return $subsets;
    }
}
?>