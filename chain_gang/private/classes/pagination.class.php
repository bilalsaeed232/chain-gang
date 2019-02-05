<?php

class Pagination {
    public $current_page;
    public $total_records;
    public $per_page;


    public function __construct($current=1, $per_page = 10, $total_records=0) {
        $this->current_page = $current;
        $this->per_page = $per_page;
        $this->total_records = $total_records;
    }


    public function total_pages() {
        return ceil($this->total_records / $this->per_page);
    }

    public function offset() {
        return $this->per_page * ($this->current_page - 1);
    }

    public function next_page() {
        $next = $this->current_page + 1;
        return ($next <= $this->total_pages() ? $next: false);
    }


    public function previous_page() {
        $prev = $this->current_page - 1;
        return ($prev > 0 ? $prev : false);
    }

    public function next_link($url) {

        $link = "";
        if ($this->next_page() != false) {

            $link .= "<a href='{$url}?page={$this->next_page()}'>";
            $link .= "Next &raquo;";
            $link .= "</a>";
        }

        return $link;
    }

    public function previous_link($url) {

        $link = "";
        if($this->previous_page() != false) {

            $link .= "<a href='{$url}?page={$this->previous_page()}'>";
            $link .= "&laquo; Previous";
            $link .= "</a>";
        }

        return $link;
    }

    public function numbered_links($url) {
        $output = "";

        for ($i=1; $i <= $this->total_pages() ; $i++) { 
            if ($i == $this->current_page) {
                $output .= "<span class='selected'>{$i}</span>";
            } else {
                $output .= "<a href='{$url}?page={$i}'>{$i}</a>";
            }
        }

        return $output;
    }

    public function page_links($url="") {
        $output = "";
        if($url!= ""){
            $output .= "<div class='pagination'>";
            $output .= $this->previous_link($url);
            $output .= $this->numbered_links($url);
            $output .= $this->next_link($url);
            $output .= "</div>";
        }
        return $output;
    }



}



?>