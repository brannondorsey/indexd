class InfoDisplay {

  int x, y, h, w;
  PFont font;

  //set up the size of the dark text box in the constructor
  InfoDisplay(int _x, int _y, int _w, int _h) {
    font = loadFont("FranklinGothic-Book-48.vlw");
    x = _x;
    y = _y;
    w = _w;
    h = _h;
  }

  //display user's information in the text box.
  //Called from inside draw when a UserBall is hovered over
  void display(UserBall user) {
    int textX = x;
    int textY = y;
    String text;
    int marginTop = 45;
    int smallMargin = 7;
    int marginLeft = 25;
    textX += marginLeft;
    textY += marginTop;

    //font sizes
    int nameSize = 26;
    int mediaSize = 18;
    int tagsSize = mediaSize;
    int locationSize = 20;

    //colors
    color boxColor = 0;
    int boxAlpha = 100;
    color nameColor = largeEllipseColor;
    color mediaColor = 255;
    color tagsColor = mediaColor;
    color locationColor = largeEllipseColor;

    //draw background box
    noStroke();
    fill(boxColor, boxAlpha);
    rect(x, y, w, h);

    //draw user's name
    fill(nameColor);
    textFont(font, nameSize);
    text = user.firstName + " " +user.lastName;
    text(text, textX, textY);

    strokeWeight(1);
    stroke(nameColor);
    line(textX, textY+smallMargin+5, textX+textWidth(text), textY+smallMargin+5);

    //update start position
    textY += nameSize + smallMargin+5;

    //draw user's media
    fill(mediaColor);
    textFont(font, mediaSize);
    text = "Media: " + join(user.media, ", ");
    text(text, textX, textY);

    //update start position
    textY += mediaSize+smallMargin;

    //draw user's tags
    fill(tagsColor);
    textFont(font, tagsSize);
    text = "Tags: " + join(user.tags, ", ");
    text(text, textX, textY);

    //update start position
    textY += tagsSize+smallMargin*2;

    //draw user's location
    fill(locationColor);
    textFont(font, locationSize);
    text = user.city + ", " + user.state;
    text(text, textX, textY);
  }

  void displayError(String errorString) {
    int errorSize = 24;
    fill(255);
    textFont(font, errorSize);
    float errorWidth = textWidth(errorString);
    text(errorString, (width/2)-(errorWidth/2), (height/2)+(errorSize/2));
  }
}

