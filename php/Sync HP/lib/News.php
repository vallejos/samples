<?php


/**
 * Description of News
 *
 * @author fernando
 */
class News {
    const URL_IMGS = ' http://maxx.me.net-m.net/ms/pub/media/mebc200/';

    private $id;

    /**
     *
     * @var Array: date => Fecha desde la cual es valida, time => hora desde la cual es valida
     */
    private $validFrom;
    /**
     *
     * @var Array: date => Fecha hasta la cual es valida, time => hora hasta la cual es valida
     */
    private $validTo;
    private $img;
    private $title;
    private $summary;
    private $body;

    function __construct($id, $img, $title, $summary, $body) {
        $this->id = $id;
        $this->img = $img;
        $this->title = $title;
        $this->summary = $summary;
        $this->body = $body;
    }

    public function getImagePath(){
       return News::URL_IMGS.$this->img;
    }



    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getValidFrom() {
        return $this->validFrom;
    }

    public function setValidFrom($validFrom) {
        $arrValid = explode(" ", $validFrom);
        $this->validFrom['date'] = $arrValid[0];
        $this->validFrom['time'] = $arrValid[1];
    }

    public function getValidTo() {
        return $this->validTo;
    }

    public function setValidTo($validTo) {
        $arrValid = explode(" ", $validTo);

        $this->validTo['date'] = $arrValid[0];
        $this->validTo['time'] = $arrValid[1];
    }

    public function getImg() {
        return $this->img;
    }

    public function setImg($img) {
        $this->img = $img;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getSummary() {
        return $this->summary;
    }

    public function setSummary($summary) {
        $this->summary = $summary;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }



}
?>
