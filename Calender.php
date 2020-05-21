<?php

namespace MyApp;
class Calendar
{
    public $prev;
    public $next;
    public $yearMonth;
    private $thisMonth;

    public function __construct()
    {
        $yearMonth = isset($_GET['t']) ? $_GET['t'] : null;
        try{
            if ($yearMonth) {
                //不正な引数が指定された場合はエラーを表示する
                $this->thisMonth = new \DateTime($yearMonth);
            } else {
                $this->thisMonth = new \DateTime('first day of this month');
            }
        } catch(\Exception $e) {
            echo 'カレンダーの表示に失敗しました。';
        }

        $this->prev = $this->createPrevlink();
        $this->next = $this->createNextlink();
        $this->yearMonth = $this->thisMonth->format('F Y');
    }

    public function createPrevlink()
    {
        $dt = clone $this->thisMonth;
        return $dt->modify('-1 month')->format('Y-m');
    }

    public function createNextlink()
    {
        $dt = clone $this->thisMonth;
        return $dt->modify('+1 month')->format('Y-m');
    }

    public function show()
    {
        $tail = $this->getTail();
        $body = $this->getBody();
        $head = $this->getHead();
        $html = '<tr>' . $tail . $body . $head . '</tr>';
        echo $html;
    }

    private function getTail()
    {
        $tail = '';
        $lastDayOfPrevMonth = new \Datetime('last day of' . $this->yearMonth . '-1 month');
        while ($lastDayOfPrevMonth->format('w') < 6) {
            $tail = sprintf('<td class="gray">%d</td>', $lastDayOfPrevMonth->format('d')) . $tail;
            $lastDayOfPrevMonth->sub(new \DateInterval('P1D'));
        }
        return $tail;
    }

    private function getBody()
    {
        $body = '';
        $period = new \DatePeriod(
            new \DateTime('first day of' . $this->yearMonth),
            new \DateInterval('P1D'),
            new \DateTime('first day of' . $this->yearMonth . '+1 month')
        );
        $today = new \DateTime('today');
        foreach ($period as $day) {
            if ($day->format('w') === '0') {
                $body .= '</tr><tr>';
            }
            $todayClass = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';
            $body .= sprintf('<td class="youbi_%d %s">%d</td>', $day->format('w'), $todayClass, $day->format('d'));
        }
        return $body;
    }

    private function getHead()
    {
        $head = '';
        $firstDayOfNextMonth = new \Datetime('first day of' . $this->yearMonth . '+1 month');
        while ($firstDayOfNextMonth->format('w') > 0) {
            $head .= sprintf('<td class="gray">%d</td>', $firstDayOfNextMonth->format('d'));
            $firstDayOfNextMonth->add(new \DateInterval('P1D'));
        }
        return $head;
    }
}