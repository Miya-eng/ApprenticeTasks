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
            // //手札の配布 ###############
            // $hand = $this->cards->giveCards(2);
            // for ($i = 1; $i < 3; $i++) {
            //     $this->players[] = new Player("プレイヤー{$i}");
            // }
            // foreach ($this->players as $index => $player) {
            //     $player->setHand($hand[$index]);
            // }
        }

        public function play() {
            //開始の合図
            echo "戦争を開始します。\n";
            //プレイヤー人数の入力
            while (true) {
                echo "プレイヤーの人数を入力してください（2〜5）: ";
                $playerNum = (int) fgets(STDIN);
                if ($playerNum >= 2 && $playerNum <= 5) {
                    break;
                } else {
                    echo "1人または5人以上でのプレイはできません。再度プレイヤーの人数を入力してください。\n";
                }
            }
            //プレイヤー名の入力
            for ($i = 1; $i <= $playerNum; $i++) {
                echo "プレイヤー{$i}の名前を入力してください: ";
                $name = trim(fgets(STDIN));
                $this->players[] = new Player($name);
            }
            //各プレイヤーの手札を生成
            $hand = $this->cards->giveCards($playerNum);
            foreach ($this->players as $index => $player) {
                $player->setHand($hand[$index]);
            }
            echo "カードが配られました。\n";
            //プレイヤーはカードを一枚出し、場に出されたカードの強さを比べ、勝者の宣言
            $field = [];
            while (array_filter($this->players, fn($player) => $player->hasCards())) {
                echo "戦争！\n";
                $maxValue = 0;
                $winners = [];
                $winnersCard = [];
                // $field = [];
                foreach ($this->players as $index => $player) {
                    if (!$player->hasCards()) {
                        echo "{$player->getName()}の手札がなくなりました。\n";
                        continue;
                    } 
                    // $field = $player->setField();
                    $card = $player->setField();
                    $field[] = $card;
                    // $player->setWonCards($card);
                    echo "{$player->getName()}のカードは{$card}です。\n";
                    if ($card->getRank() > $maxValue) {
                        $maxValue = $card->getRank();
                        $winners = [$player];
                        $winnersCard = [$card];
                    } else if ($card->getRank() === $maxValue) {
                        if ($maxValue === 14) {
                            // if ($card->getSuitRank() > $maxValue) {
                            //     $winners = [$player];
                            // }
                            if ($card->getSuitRank() > $winnersCard[0]->getSuitRank()) {
                                $winners = [$player];
                            }
                        } else {
                            $winners[] = $player;
                        }
                    }
                }
                
                // if(!$player || $player->hasCards()) {
                if (count($winners) === 1) {
                    // $winner->newHand();
                    $winner = $winners[0];
                    $currentHand = array_merge($winner->getHand(), $field);
                    shuffle($currentHand);
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
                // **ゲーム終了判定**
                $remainingPlayers = array_filter($this->players, fn($player) => $player->hasCards());
                if (count($remainingPlayers) === count($this->players) - 1) {
                    break; // 残り1人になったらループを終了
        }
            }
            // ゲーム終了
            $this->endPlay();
        }
        // ゲーム終了
        public function endPlay() {
            $playerRanks = [];
            foreach ($this->players as $player) {
                // echo "{$player->getName()}の手札の枚数は{$player->countHand()}枚です。\n";
                $playerRanks[$player->getName()] = $player->countHand();
            }
            arsort($playerRanks);
            $rank = 1;
            foreach ($playerRanks as $name => $handCount) {
                echo "{$rank}位:{$name}（{$handCount}枚）\n";
                $rank++;
            }
            echo "戦争を終了します。\n";
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

            $this->cards[] = new Rank(null, "Joker");
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
                       "10" => 10, "J" => 11, "Q" => 12, "K" => 13, "A" => 14, "Joker" => 15];
            return $ranks[$this->number];
        }

        public function getSuitRank() {
            $ranks = ["ハート" => 1 , "スペード" => 2, "ダイヤ" => 1 , "クローバー" => 1];
            return $ranks[$this->suit] ?? 0;
        }
    
        public function __toString() {
            if ($this->suit) {
                return "{$this->suit}の{$this->number}";
            } else {
                return "{$this->number}";
            }
        }
    }
    //参加者
    class Player {
        private $name;
        private $hand = [];
        // private $wonCards = [];
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