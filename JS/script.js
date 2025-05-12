let canvas;

function setup() {
    canvas = createCanvas(windowWidth / 3, windowWidth / 3);
    positionCanvas();
    background(0);
}

function windowResized() {
    resizeCanvas(windowWidth / 3, windowWidth / 3);
    positionCanvas();
    background(0);
}

function px(number) {
    if (windowWidth <= 650) {
        return number * (windowWidth) * 0.8 / 100;
    } else {
        return number * (windowWidth / 3) / 100;
    }
}

function positionCanvas() {
    if (windowWidth <= 650) {
        resizeCanvas(windowWidth * 0.8, windowWidth * 0.8);
        const xPos = (windowWidth - width) / 2;
        const yPos = (windowHeight - height) / 2;
        canvas.position(xPos, yPos);
    } else {
        const xPos = windowWidth / 2 + (windowWidth / 4) - width / 2;
        const yPos = (windowHeight / 2) - height / 2;
        canvas.position(xPos, yPos);
    }
}

function draw() {
    fill(255, 0, 0);
    noStroke();
    ellipse(px(15), px(30), px(5), px(5));
}