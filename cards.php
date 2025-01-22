<?php
    /**トランプゲームの戦争**/
        //STEP1
        //開始の合図、手札の配布
        //「戦争！」の掛け声とともに、各プレイヤーは手札束のカードを、場に置きます。
        //出したカードの強さの大小を比べて、一番強いカードを出した人が勝ちとなり、場にあるカードをもらいます。
        //一番強い数値が複数出た場合、もう一度手札からカードを出します。そして、勝ったプレイヤーが場札をもらいます。
        //同じ数字が続いたら、勝ち負けが決まるまでカードを出します。
        //勝ち負けが決まったら、勝ったプレイヤーの名前を表示してください。今回は一回のみの勝負。

    //ゲームの進行
    class Game {
        private $cards;
        private $players = [];

        //ゲームの準備（初期化）
        public function __construct() {
            $this->cards = new Cards();
            $this->cards->makeCards();
            $this->cards->shuffle();
            //手札の配布 ###############
            $hand = $this->cards->giveCards(2);
            for ($i = 1; $i < 3; $i++) {
                $this->players[] = new Player("プレイヤー{$i}");
            }
            foreach ($this->players as $index => $player) {
                $player->setHand($hand[$index]);
            }
        }

        public function play() {
            $field = [];
            echo "戦争を開始します。\n";
            echo "カードが配られました。\n";
            //プレイヤーはカードを一枚出し、場に出されたカードの強さを比べ、勝者の宣言
            while (true) {
                echo "戦争！\n";
                $maxValue = 0;
                $winner = null;
                foreach ($this->players as $index => $player) {
                    $field = $player->setField();
                    echo "プレイヤー" . ($index + 1) . "のカードは{$field}です。\n";
                    if ($field->getRank() > $maxValue) {
                        $maxValue = $field->getRank();
                        $winner = $player;
                    } else if ($field->getRank() === $maxValue) {
                        $winner = null;
                    }
                }
    
                if ($winner) {
                    echo "{$winner->getName()}が勝ちました。\n戦争を終了します。";
                    break;
                } else {
                    echo "引き分けです。\n";
                }
            }
        }
    }

    //トランプの生成
    class Cards {
        private $cards = [];
        public function makeCards() {
            $suits = ["ハート", "スペード", "ダイヤ", "クローバー"];
            $numbers = ["2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K", "A"];

            foreach ($suits as $suit) {
                foreach ($numbers as $number) {
                    $this->cards[] = new Rank($suit, $number);
                }
            }
        }

        //トランプをシャッフル
        public function shuffle() {
            shuffle($this->cards);
        }
        //トランプを配る
        public function giveCards($numPlayer) {
            //トランプを各プレイヤーに配布
            $hands = array_fill(0, $numPlayer, []);
            $player = 0;
            while (!empty($this->cards)) {
                $hands[$player][] = array_pop($this->cards);
                $player = ($player + 1) % $numPlayer;
            }
            return $hands;
        }
    }

    //トランプのランク付け
    class Rank {
        public $suit; 
        public $number; 
        
        public function __construct($suit, $number) {
            $this->suit = $suit;
            $this->number = $number;
        }
        
        public function getRank() {
            $ranks = ["2" => 2, "3" => 3, "4" => 4, "5" => 5, "6" => 6, "7" => 7, "8" => 8, "9" => 9,
                       "10" => 10, "J" => 11, "Q" => 12, "K" => 13, "A" => 14];
            return $ranks[$this->number];
        }
    
        public function __toString() {
            return "{$this->number} of {$this->suit}";
        }
    }

    class Player {
        private $name;
        private $hand = [];
        public function __construct($name) {
            $this->name = $name;
        }

        public function getName() {
            return $this->name;
        }
        //配られた手札
        public function setHand($hand) {
            $this->hand = $hand;
        }

        //配られた手札から一枚出す
        public function setField() {
            return array_pop($this->hand);
        }
    }

    $game = new Game();
    $game->play();
?>