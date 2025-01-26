<?php
    /**トランプゲームの戦争**/
        //STEP1
        //開始の合図、手札の配布
        //「戦争！」の掛け声とともに、各プレイヤーは手札束のカードを、場に置きます。
        //出したカードの強さの大小を比べて、一番強いカードを出した人が勝ちとなり、場にあるカードをもらいます。
        //一番強い数値が複数出た場合、もう一度手札からカードを出します。そして、勝ったプレイヤーが場札をもらいます。
        //同じ数字が続いたら、勝ち負けが決まるまでカードを出します。
        //勝ち負けが決まったら、勝ったプレイヤーの名前を表示してください。今回は一回のみの勝負。

        //STEP2
        // 誰かの手札がなくなったらゲーム終了し、順位を表示するようにしましょう。この時点での手札の枚数が多い順に1位、2位、・・・という順位になります。

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
                // $field = [];
                foreach ($this->players as $index => $player) {
                    if (!$player->hasCards()) {
                        continue;
                    }
                    // $field = $player->setField();
                    $card = $player->setField();
                    $field[] = $card;
                    // $player->setWonCards($card);
                    echo "プレイヤー" . ($index + 1) . "のカードは{$card}です。\n";
                    if ($card->getRank() > $maxValue) {
                        $maxValue = $card->getRank();
                        $winner = $player;
                    } else if ($card->getRank() === $maxValue) {
                        $winner = null;
                    }
                }
                
                // if(!$player || $player->hasCards()) {
                if ($winner) {
                    // $winner->newHand();
                    $currentHand = array_merge($winner->getHand(), $field);
                    $winner->setHand($currentHand);
                    $quantity = count($field);
                    echo "{$winner->getName()}が勝ちました。{$winner->getName()}はカードを{$quantity}枚もらいました\n";
                    // $player->resetWonCards();
                    $field = [];
                } else {
                    echo "引き分けです。\n";
                } 
                // } else {
                //     echo "{$player->getName()}の手札がなくなりました。\n";
                //     echo "{$winner->getName()}の手札の枚数は{$winner->countHand()}枚です。{$player->getName()}の手札の枚数は0枚です。\n";
                //     echo "{$winner->getName()}が1位、{$player->getName()}が2位です。\n";
                //     echo "戦争を終了します。\n";
                //     break;
                // }

                // ゲーム終了条件
                $remainingPlayers = array_filter($this->players, fn($player) => $player->hasCards());
                if (count($remainingPlayers) === 1) {
                    $winner = reset($remainingPlayers);
                    echo "{$winner->getName()}が1位です！\n";
                    echo "戦争を終了します。\n";
                    break;
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
    //参加者
    class Player {
        private $name;
        private $hand = [];
        private $wonCards = [];
        public function __construct($name) {
            $this->name = $name;
        }
        //参加者の名前を取得
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
        //現在の手札
        public function getHand() {
            return $this->hand;
        }
        //手札のカウント
        public function countHand() {
            return count($this->hand);
        }
        // //勝った場合の場札を管理
        // public function setWonCards($wonCards) {
        //     $this->wonCards[] = $wonCards;
        // }
        // //場札のリセット
        // public function resetWonCards() {
        //     $this->wonCards = [];
        // }
        // //勝ち取った場札のカウント
        // public function countWonCards() {
        //     return count($this->wonCards);
        // }
        //現在の手札に勝ち取った場札を足す
        // public function newHand() {
        //     $this->hand = array_merge($this->hand, $this->wonCards);
        // }
        //手札の有無を判定
        public function hasCards() {
            return !empty($this->hand);
        }
    }

    $game = new Game();
    $game->play();
?>