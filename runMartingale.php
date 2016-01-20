<?php
/*
The MIT License (MIT)

Copyright (c) 2016 Nixtren

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
function runMartingale($base_bet, $balance, $bets) // Uses bcmath for arbitrary precision
{
	bcscale(8);
	$betsTaken = 0;
	$currentBet = $base_bet;
	$initialBalance = $balance;
	$current_win_row = 0;
	$current_loss_row = 0;
	$max_win_row = 0;
	$max_loss_row = 0;
	$betsWon = 0;
	$betsLost = 0;
	while($betsTaken < $bets)
	{
		$betsTaken++;
		$balance_ = bcsub($balance, $currentBet); // Take bet amount from balance
		if($balance_ < 0) 
		{
			break; // No more money to bet, abort
		}
	    else
	    {
	    	$balance = $balance_;
			$win = mt_rand(0, 1000) > 490; // 490 = 49.0 % win chance
			if($win)
			{
	            $balance = bcadd($balance, bcmul($currentBet, "2")); // Put bet amount (+ profit) to balance.
	            $currentBet = $base_bet;
	            $current_loss_row = 0;
	            $current_win_row++;
	            if($max_win_row < $current_win_row) $max_win_row = $current_win_row;
	            $betsWon++;
			}
			else
			{
				$currentBet = bcmul($currentBet, "2"); // Double current bet (Martingale)
				$current_win_row = 0;
				$current_loss_row++;
				if($max_loss_row < $current_loss_row) $max_loss_row = $current_loss_row;
                $betsLost++;
			}
		}
	}
	return array("expectedBets" => (int) $bets, "betsTaken" => $betsTaken, "balance" => $balance, "profit" => bcsub($balance, $initialBalance), "lossStrike" => $max_loss_row);
}
if(isset($argv[0]) && $argv[0] == "runMartingale.php") // Ability to run this class through the console directly
{
	if(!empty($argv[1]) && !empty($argv[2]) && !empty($argv[3]) && empty($argv[4])) print_r(runMartingale($argv[1], $argv[2], $argv[3]));
    else if(!empty($argv[4]))
    {
    	for($i = 0; $i != $argv[4]; $i++) echo runMartingale($argv[1], $argv[2], $argv[3])["profit"] . "\n";
    }
    else echo "Usage: php {$argv[0]} base_bet balance bets [rounds]\n";
}