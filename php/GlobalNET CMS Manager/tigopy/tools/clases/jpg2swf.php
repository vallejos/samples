<?PHP
function makeImgMovie($imgFile) {
    //Make sure this file is an actual jpg file
    if (is_file($imgFile) && ereg("\.jpg$", $imgFile)) {
        //Launch Flash
        $movie = new swfMovie();
        $movie->setBackground(255, 255, 255);
        
        //Import a bitmap
        $b = new SWFBitmap(fOpen($imgFile, "rb"));
        //Get it's width and height
        $w = $b->getWidth();
        $h = $b->getHeight();

        //Make stage as big as the width and height of our bitmap
        $movie->setDimension($w, $h);

        //Convert Bitmap to a shape for Flash.
        //This process is automated upon import in Flash, but with
        //Ming we have have to do it ourselves. I see it as more control:)
        $s = new SWFShape();
        $f = $s->addFill($b);
        $s->setRightFill($f);
        //draw corners for the bitmap shape
        $s->drawLine($w, 0);
        $s->drawLine(0, $h);
        $s->drawLine(-$w, 0);
        $s->drawLine(0, -$h);

        //CreateEmptyMovieclip
        $p = new SWFSprite();
        //add our bitmap shape to this movieclip
        $p->add($s);
        $p->nextFrame();

        //Add this movieclip to our main movie's timeline
        //Much like dragging a symbol from the library in Flash.
        $i = $movie->add($p);
        //Give this instance a name.. let's call it mImg as in movie image
        $i->setName("mImg");
        
        //Output the movie.. Ctrl+Enter!
       // header("Content-Type:application/x-shockwave-flash");
        $movie->output();
    }//End if
}//End function



?>