let canvas;
let roadbg;
let priorGameInitialized = false;
let placementOrder = [];
let feedbackLight = null;
let itemSpots = [];
let items = [];
let objectGameInitialized = false;
let growingSign;
let slider;
let sliderValue = 10;
let parkTargets = [];

let priorVehicles = [];
let priorTargets = [];
let priorSigns = [];
let priorStreetMarkings = [];

let targetZoneTop, targetZoneLeft, targetZoneBottom, targetZoneRight;

let imageCache = {};

let signTrafficSign = null;
let signSpeed = null;

let signTimerStart = null;
let signTimerDuration = 5; 
let signTimerActive = false;
let signTimerDone = false;

let sliderContainer;

let blinkTargetIndex = null;
let blinkStartTime = 0;
let blinkDuration = 2000; // ms
let blinkInterval = 300;  // ms

let priorBlinkVehicleIndex = null;
let priorBlinkZoneIndex = null;
let priorBlinkStartTime = 0;
let priorBlinkDuration = 2000; // ms
let priorBlinkInterval = 300;  // ms

let playerScore = 0;
let questionResults = []; // "correct" or "incorrect"

let showGreenCheck = false;

function preload() {
    roadbg = loadImage('https://i.imgur.com/FlS2QeG.png');
    tableImg = loadImage('https://i.imgur.com/l521dGL.png');
    signbg = loadImage('https://i.imgur.com/SzUvDYF.png');
    parkRoadbg = loadImage('https://i.imgur.com/FNJqNZ1.png'); // changed here
    parkCarImg = loadImage('https://i.imgur.com/1AczEtv.png');
}

function setup() {
    canvas = createCanvas(windowWidth / 3, windowWidth / 3);
    positionCanvas();
    background(0);

    targetZoneTop    = { x: px(35), y: px(0),  width: px(30), height: px(30) };
    targetZoneLeft   = { x: px(0),  y: px(35), width: px(30), height: px(30) };
    targetZoneBottom = { x: px(35), y: px(70), width: px(30), height: px(30) };
    targetZoneRight  = { x: px(70), y: px(35), width: px(30), height: px(30) };

    sliderContainer = createDiv();
    sliderContainer.style('background', '#E0B44A');
    sliderContainer.style('border-radius', '12px');
    sliderContainer.style('padding', '16px 24px'); 
    sliderContainer.style('position', 'absolute');
    sliderContainer.style('z-index', '10');

    sliderContainer.position(
        width / 2 - px(20) + canvas.position().x - 24,
        height / 2 + px(30) + px(7) + canvas.position().y 
    );

    slider = createSlider(10, 100, 10, 10);
    slider.parent(sliderContainer);
    slider.style('width', px(40) + 'px');
    slider.style('background', 'transparent');
    slider.style('outline', 'none');
    slider.style('border', 'none');
    slider.style('appearance', 'none');
    slider.elt.style.background = 'transparent';
    slider.elt.style.height = '4px';

    slider.style('display', 'block');
    slider.style('margin', '0 auto');
    slider.style('position', 'relative');
    slider.style('top', '55%');
    slider.style('transform', 'translateY(-50%)');

    const style = document.createElement('style');
    style.innerHTML = `
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 17px;
        height: 17px;
        border-radius: 50%;
        background: #fff;
        cursor: pointer;
        box-shadow: 0 0 2px #0008;
        border: none;
        margin-top: -6.5px;
    }
    input[type=range]::-moz-range-thumb {
        width: 17px;
        height: 17px;
        border-radius: 50%;
        background: #fff;
        cursor: pointer;
        border: none;
        box-shadow: 0 0 2px #0008;
    }
    input[type=range]::-ms-thumb {
        width: 17px;
        height: 17px;
        border-radius: 50%;
        background: #fff;
        cursor: pointer;
        border: none;
        box-shadow: 0 0 2px #0008;
    }
    input[type=range]::-webkit-slider-runnable-track {
        height: 4px;
        background: #fff;
        border-radius: 4px;
    }
    input[type=range]::-ms-fill-lower {
        background: #fff;
    }
    input[type=range]::-ms-fill-upper {
        background: #fff;
    }
    input[type=range]::-moz-range-track {
        height: 4px;
        background: #fff;
        border-radius: 4px;
    }
    input[type=range] {
        background: transparent;
    }
    `;
    document.head.appendChild(style);

    slider.hide();

   
    if (window.questions) {
        for (const q of questions) {
            [
                'vehicleTopSprite', 'vehicleLeftSprite', 'vehicleRightSprite', 'vehicleBottomSprite',
                'backgroundSprite',
                'Spot1_Sprite', 'Spot2_Sprite', 'Spot3_Sprite', 'Spot4_Sprite', 'Spot5_Sprite', 'Spot6_Sprite'
            ].forEach(key => {
                if (q[key] && !imageCache[q[key]]) {
                    imageCache[q[key]] = loadImage(q[key]);
                }
            });
        }
    }
    if (typeof showQuestion === "function") {
        showQuestion(currentIndex);
    }
}

function windowResized() {
    resizeCanvas(windowWidth / 3, windowWidth / 3);
    positionCanvas();
    background(0);

   
    targetZoneTop    = { x: px(35), y: px(0),  width: px(30), height: px(30) };
    targetZoneLeft   = { x: px(0),  y: px(35), width: px(30), height: px(30) };
    targetZoneBottom = { x: px(35), y: px(70), width: px(30), height: px(30) };
    targetZoneRight  = { x: px(70), y: px(35), width: px(30), height: px(30) };
}

function px(number) {
    if (windowWidth <= 650) {
        return number * (windowWidth) * 0.5 / 100;
    } else {
        return number * (windowWidth / 3) / 100;
    }
}

function positionCanvas() {
    if (windowWidth <= 650) {
        const canvasSize = windowWidth * 0.5; // 70vw
        resizeCanvas(canvasSize, canvasSize);

        // Center horizontally
        const xPos = (windowWidth) / 2 - canvasSize / 2;
        const topOffset = 170;
        const bottomOffset = 80;
        const availableHeight = windowHeight - topOffset - bottomOffset;
        const yPos = availableHeight > 0
            ? topOffset + (availableHeight - canvasSize) / 2
            : topOffset;
        canvas.position(xPos, yPos);
    } else {
        // DESKTOP: Center in the middle of the left half of the screen, but 20% lower
        const canvasSize = windowWidth / 3;
        resizeCanvas(canvasSize, canvasSize);
        const xPos = windowWidth / 4 - canvasSize / 2;
        const yPos = (windowHeight - canvasSize) / 2 + windowHeight * 0.1;
        canvas.position(xPos, yPos);
    }
}

class Vehicle {
    constructor(image, x, y, width, height, targetZone, priority) {
        this.image = image;
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
        this.isDragging = false;
        this.targetZone = targetZone;
        this.inTargetZone = false;
        this.priority = priority;
        this.locked = false; // <-- Add this line
    }

    mousePressed() {
        if (
            !this.inTargetZone && 
            !this.locked && // <-- Add this check
            mouseX > this.x &&
            mouseX < this.x + this.width &&
            mouseY > this.y &&
            mouseY < this.y + this.height
        ) {
            this.isDragging = true;
        }
    }

    mouseReleased() {
        this.isDragging = false;
        this.checkInTargetZone();
    }

    drag() {
        if (this.isDragging && !this.inTargetZone && !this.locked) { // <-- Add !this.locked
            this.x = mouseX - this.width / 2;
            this.y = mouseY - this.height / 2;
        }
    }

    checkInTargetZone() {
        if (
            this.x + this.width > this.targetZone.x &&
            this.x < this.targetZone.x + this.targetZone.width &&
            this.y + this.height > this.targetZone.y &&
            this.y < this.targetZone.y + this.targetZone.height
        ) {
            if (!this.inTargetZone) {
                if (!placementOrder.includes(this.priority)) {
                    placementOrder.push(this.priority);
                }
                this.inTargetZone = true;
                this.checkPlacementOrder();
            } 
        } else {
            if (this.inTargetZone) {
                const idx = placementOrder.indexOf(this.priority);
                if (idx !== -1) placementOrder.splice(idx, 1);
            }
            this.inTargetZone = false;
            this.checkPlacementOrder(); 
        }
    }

    checkPlacementOrder() {
        const q = questions[currentIndex];
        let required = 0;
        if (q.Bottom_Priority !== undefined && q.Bottom_Priority !== null) required++;
        if (q.Right_Priority !== undefined && q.Right_Priority !== null) required++;
        if (q.Left_Priority !== undefined && q.Left_Priority !== null) required++;
        if (q.Top_Priority !== undefined && q.Top_Priority !== null) required++;

        // Reset prior blinks
        priorBlinkVehicleIndex = null;
        priorBlinkZoneIndex = null;

        if (placementOrder.length === 0) {
            feedbackLight = null;
            return;
        }
        for (let i = 0; i < placementOrder.length; i++) {
            if (placementOrder[i] !== i + 1) {
                feedbackLight = 'red';
                // Lock all vehicles
                for (let v of priorVehicles) {
                    v.locked = true;
                }
                // Find the vehicle with priority i+1 and blink it
                const blinkIndex = priorVehicles.findIndex(v => v.priority === i + 1);
                if (blinkIndex !== -1) {
                    priorBlinkVehicleIndex = blinkIndex;
                    priorBlinkStartTime = millis();
                }
                return;
            }
        }
        if (placementOrder.length === required) {
            feedbackLight = 'green';
            showGreenCheck = true;
        }
    }

    draw() {
        if (game === "park" || !this.inTargetZone) {
            image(this.image, this.x, this.y, this.width, this.height);
        }
    }
}

// Add this helper function somewhere near your Vehicle class
function isVehicleInZone(vehicle, zone) {
    return (
        vehicle.x + vehicle.width > zone.x &&
        vehicle.x < zone.x + zone.width &&
        vehicle.y + vehicle.height > zone.y &&
        vehicle.y < zone.y + zone.height
    );
}

class Target {
    constructor(x, y, width, height) {
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
    }
}

class ItemSpot {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.width = px(15);   
        this.height = px(30);
    }
}

class Item {
    constructor(image, assignedSpot, necessary) {
        this.image = image;
        this.assignedSpot = assignedSpot; 
        this.necessary = necessary;
        this.width = px(20);
        this.height = px(30);
        this.x = assignedSpot.x;
        this.y = assignedSpot.y;
        this.isDragging = false;
        this.removed = false;
        this.locked = false; // <-- Add this line
    }

    mousePressed() {
        if (
            !this.removed &&
            !this.locked && // <-- Add this check
            mouseX > this.x &&
            mouseX < this.x + this.width &&
            mouseY > this.y &&
            mouseY < this.y + this.height
        ) {
            this.isDragging = true;
        }
    }

    mouseReleased() {
        if (this.isDragging) {
            this.isDragging = false;
            if (this.y < height / 2) {
                this.removed = true;
                // If this is a necessary item, lock all items
                if (this.necessary) {
                    for (let item of items) {
                        item.locked = true;
                    }
                }
            }
        }
    }

    drag() {
        if (this.isDragging && !this.removed && !this.locked) {
            this.x = mouseX - this.width / 2;
            this.y = mouseY - this.height / 2;
        }
    }

    draw() {
        if (!this.removed) {
            image(this.image, this.x, this.y, this.width, this.height);
        }
    }
}

class GrowingSign {
    constructor(image) {
        this.image = image;
        this.minSize = px(20);
        this.maxSize = px(40);
    }

    draw() {
        let elapsed = 0;
        if (signTimerStart !== null) {
            elapsed = (millis() - signTimerStart) / 1000;
        }
        let t = constrain(elapsed / signTimerDuration, 0, 1);
        let size = lerp(this.minSize, this.maxSize, t);
        let x = width / 2 - size / 2;
        let y = height / 2 - size / 2 - px(10);
        image(this.image, x, y, size, size);
    }
}

function mousePressed() {
    if (game === "prior") {
        for (let v of priorVehicles) {
            v.mousePressed();
        }
    }
    if (game === "object") {
        for (let item of items) {
            item.mousePressed();
        }
    }
    if (game === "park") {
        if (typeof parkVehicle !== "undefined") {
            parkVehicle.mousePressed();
        }
    }
}

function mouseReleased() {
    if (game === "prior") {
        for (let v of priorVehicles) {
            v.mouseReleased();
            // No blinking logic here! Let checkPlacementOrder() handle it.
        }
    }
    if (game === "object") {
        for (let item of items) {
            item.mouseReleased();
        }
        if (checkItemsRemovedCorrectly()) {
            feedbackLight = 'green';
            showGreenCheck = true;
        } else {
            feedbackLight = 'red';
        }
    }
    if (game === "park") {
        if (typeof parkVehicle !== "undefined") {
            parkVehicle.mouseReleased();

            let onTarget = false;
            let correctIndex = questions[currentIndex].Target - 1;
            for (let i = 0; i < parkTargets.length; i++) {
                let t = parkTargets[i];
                if (
                    parkVehicle.x + parkVehicle.width > t.x &&
                    parkVehicle.x < t.x + t.width &&
                    parkVehicle.y + parkVehicle.height > t.y &&
                    parkVehicle.y < t.y + t.height
                ) {
                    onTarget = true;
                    if (i === correctIndex) {
                        feedbackLight = 'green';
                        showGreenCheck = true;
                    } else {
                        feedbackLight = 'red';
                        parkVehicle.locked = true; // <-- Lock the car
                        blinkTargetIndex = correctIndex; // <-- Set blinking target
                        blinkStartTime = millis(); // <-- Start blinking timer
                    }
                    console.log('Feedback:', feedbackLight);
                    break;
                }
            }
            if (!onTarget) {
                feedbackLight = null;
                console.log('Feedback:', feedbackLight);
            }
        }
    }
}

function draw() {
    clear();
    if (game === "prior" && questions.length > 0) {
        const q = questions[currentIndex];

        const destMap = {
            "top": targetZoneTop,
            "left": targetZoneLeft,
            "right": targetZoneRight,
            "bottom": targetZoneBottom
        };

        const vehicles = [
            q.vehicleBottomSprite && {
                sprite: q.vehicleBottomSprite,
                pos: {x: px(52), y: px(70), width: px(15), height: px(30)},
                priority: q.Bottom_Priority !== undefined && q.Bottom_Priority !== null ? Number(q.Bottom_Priority) : null,
                dest: q.Bottom_Destination ? destMap[q.Bottom_Destination.toLowerCase()] : null
            },
            q.vehicleRightSprite && {
                sprite: q.vehicleRightSprite,
                pos: {x: px(70), y: px(35), width: px(30), height: px(15)},
                priority: q.Right_Priority !== undefined && q.Right_Priority !== null ? Number(q.Right_Priority) : null,
                dest: q.Right_Destination ? destMap[q.Right_Destination.toLowerCase()] : null
            },
            q.vehicleLeftSprite && {
                sprite: q.vehicleLeftSprite,
                pos: {x: px(0), y: px(50), width: px(30), height: px(15)},
                priority: q.Left_Priority !== undefined && q.Left_Priority !== null ? Number(q.Left_Priority) : null,
                dest: q.Left_Destination ? destMap[q.Left_Destination.toLowerCase()] : null
            },
            q.vehicleTopSprite && {
                sprite: q.vehicleTopSprite,
                pos: {x: px(38), y: px(0), width: px(15), height: px(30)},
                priority: q.Top_Priority !== undefined && q.Top_Priority !== null ? Number(q.Top_Priority) : null,
                dest: q.Top_Destination ? destMap[q.Top_Destination.toLowerCase()] : null
            }
        ].filter(Boolean);

        let args = [];
        for (const v of vehicles) {
            args.push(getCachedImage(v.sprite), v.pos, v.priority, v.dest);
        }

        if (q.backgroundSprite && getCachedImage(q.backgroundSprite)) {
            background(getCachedImage(q.backgroundSprite));
        } else {
            background(roadbg);
        }
        priorGame(...args);

        if (slider) slider.hide();
    }
    if (game == "object") {
        objectGame();
        if (slider) slider.hide();
    }
    if (game == "sign") {
        background(signbg);
        signGame();
        if (slider) slider.show();
        if (sliderContainer) sliderContainer.show();
    } else {
        if (slider) slider.hide();
        if (sliderContainer) sliderContainer.hide();
    }
    if (game == "park") {
        if (typeof parkVehicle !== "undefined") {
            parkVehicle.drag();
            parkVehicle.draw();
        }
        parkGame();
        if (slider) slider.hide(); 
    }

    let timeLeft = 0;
    if (signTimerActive && signTimerStart !== null) {
        let elapsed = (millis() - signTimerStart) / 1000;
        timeLeft = max(0, signTimerDuration - elapsed);

        let cx = width - px(6); 
        let cy = px(6);         
        let r = px(4); 
        let angle = map(timeLeft, 0, signTimerDuration, 0, TWO_PI);

        noFill();
        stroke(200);
        strokeWeight(2);
        ellipse(cx, cy, r * 2, r * 2);

        noStroke();
        fill(255, 255, 255, 255); 
        arc(cx, cy, r * 2, r * 2, -HALF_PI, -HALF_PI + angle, PIE);

        if (timeLeft <= 0 && !signTimerDone) {
            signTimerActive = false;
            signTimerDone = true;
            const q = questions[currentIndex];
            if (Number(slider.value()) === Number(q.signSpeed)) {
                feedbackLight = 'green';
                showGreenCheck = true;
            } else {
                feedbackLight = 'red';
            }
            console.log(feedbackLight);
        }
    }

    if (showGreenCheck) {
        drawGreenCheckmark(width / 2, height / 2, Math.min(width, height) * 0.3);
    }

    if (game === "object" && feedbackLight === 'red') {
        drawRedX(width / 2, height / 2, Math.min(width, height) * 0.3);
    }
}

function priorGame(
    v1Sprite, v1Pos, v1Priority, v1Dest,
    v2Sprite, v2Pos, v2Priority, v2Dest,
    v3Sprite, v3Pos, v3Priority, v3Dest,
    v4Sprite, v4Pos, v4Priority, v4Dest,
) {
    if (!priorGameInitialized) {
        priorVehicles = [];
        priorTargets = [];

        let vehiclesData = [
            {sprite: v1Sprite, pos: v1Pos, priority: v1Priority, dest: v1Dest},
            {sprite: v2Sprite, pos: v2Pos, priority: v2Priority, dest: v2Dest},
            {sprite: v3Sprite, pos: v3Pos, priority: v3Priority, dest: v3Dest},
            {sprite: v4Sprite, pos: v4Pos, priority: v4Priority, dest: v4Dest}
        ];

        for (let v of vehiclesData) {
            if (v.sprite) {
                let target = new Target(v.dest.x, v.dest.y, v.dest.width, v.dest.height);
                priorTargets.push(target);
                let vehicle = new Vehicle(
                    v.sprite,
                    v.pos.x, v.pos.y, v.pos.width, v.pos.height,
                    target,
                    v.priority
                );
                priorVehicles.push(vehicle);
            }
        }
        priorGameInitialized = true;
    }

    if (priorGameInitialized) {
        // Draw vehicles (with blinking if needed)
        for (let vIndex = 0; vIndex < priorVehicles.length; vIndex++) {
            let v = priorVehicles[vIndex];
            let shouldBlinkVehicle = false;
            if (
                priorBlinkVehicleIndex === vIndex &&
                millis() - priorBlinkStartTime < priorBlinkDuration &&
                Math.floor((millis() - priorBlinkStartTime) / priorBlinkInterval) % 2 === 0
            ) {
                shouldBlinkVehicle = true;
            }
            if (shouldBlinkVehicle) {
                push();
                tint(255, 0, 0); // Red tint
                v.draw();
                pop();
            } else {
                v.drag();
                v.draw();
            }
        }

        // Reset blink after duration
        if (priorBlinkVehicleIndex !== null && millis() - priorBlinkStartTime > priorBlinkDuration) {
            priorBlinkVehicleIndex = null;
        }

        for (let s of priorSigns) {
            s.draw();
        }
    }
}

function objectGame() {
    if (!objectGameInitialized) {
        let spacing = (width - 4 * px(15)) / 5;
        let y = height - px(42);
        itemSpots = [];
        for (let i = 0; i < 4; i++) {
            let x = spacing + i * (px(15) + spacing);
            itemSpots.push(new ItemSpot(x, y));
        }

        const q = questions[currentIndex];
        items = [];
        for (let i = 1; i <= 4; i++) {
            const itemData = q[`Item${i}`];
            if (itemData && itemData.Image_Url) {
                let img = getCachedImage(itemData.Image_Url);
                if (!img) {
                    img = loadImage(itemData.Image_Url, loadedImg => {
                        imageCache[itemData.Image_Url] = loadedImg;
                    });
                }
                items.push(new Item(img, itemSpots[i - 1], !!itemData.Necessary));
            } else {
                items.push(new Item(null, itemSpots[i - 1], false));
            }
        }

        // Unlock all items for the new question
        for (let item of items) {
            item.locked = false;
        }

        objectGameInitialized = true; // <-- Fix: set to true after initialization
    }

    background(tableImg);

    for (let item of items) {
        item.drag();
        item.draw();
    }
}

function checkItemsRemovedCorrectly() {
    for (let item of items) {
        if (item.necessary && item.removed) {
            return false;
        }
        if (!item.necessary && !item.removed) {
            return false;
        }
    }
    return true;
}

function signGame() {
    const q = questions[currentIndex];
    if (!q || typeof q.signSpeed === "undefined") return;
    if (!signTrafficSign && q.signImageUrl) {
        loadImage(q.signImageUrl, img => {
            signTrafficSign = new GrowingSign(img);
            signTimerStart = millis();
            signTimerActive = true;
            signTimerDone = false;
        });
        return; 
    }

    if (signTrafficSign) {
        signTrafficSign.draw();
    }

    textAlign(CENTER, CENTER);
    textStyle(BOLD);
    textSize(px(7));
    let sliderVal = slider.value();
    let correctVal = q.signSpeed;
    let x = width / 2;
    let y = height / 2 + px(22);

    // If wrong, draw strikethrough and show correct answer in red
    if (signTimerDone && Number(sliderVal) !== Number(correctVal)) {
        fill(255);
        text(sliderVal, x, y);

        // Draw strikethrough
        let txtW = textWidth(sliderVal);
        stroke(255, 0, 0);
        strokeWeight(3);
        line(x - txtW / 2, y, x + txtW / 2, y);

        // Draw correct answer in red to the right, with a margin
        noStroke();
        fill(255, 0, 0);
        textStyle(BOLD);
        text(correctVal, x + txtW + px(1), y); // <-- Add px(6) for spacing
    } else {
        fill(255);
        noStroke();
        text(sliderVal, x, y);
    }
    textStyle(NORMAL);
}

function parkGame() {
    background(parkRoadbg);
    const q = questions[currentIndex];

    if (parkTargets.length === 0) {
        parkTargets.length = 0;
        let marginX = px(10);
        let targetW = px(20);
        let targetH = px(30);
        let totalTargets = 3;
        let totalHeight = totalTargets * targetH;
        let availableHeight = height - totalHeight;
        let marginY = availableHeight * 0.3;

        for (let i = 0; i < totalTargets; i++) {
            let y = marginY + i * targetH;
            parkTargets.push(new Target(marginX, y, targetW, targetH));
        }
        for (let i = 0; i < totalTargets; i++) {
            let y = marginY + i * targetH;
            parkTargets.push(new Target(width - targetW - marginX, y, targetW, targetH));
        }
    }

    // Draw parked vehicles
    for (let i = 1; i <= 6; i++) {
        const spriteUrl = q[`Spot${i}_Sprite`];
        if (spriteUrl) {
            let img = getCachedImage(spriteUrl);
            let t = parkTargets[i - 1];
            if (img && t) {
                let imgW = px(15);
                let imgH = px(30);
                let imgX = t.x + (t.width - imgW) / 2;
                let imgY = t.y + (t.height - imgH) / 2;
                image(img, imgX, imgY, imgW, imgH);
            }
        }
    }

    // Draw the draggable vehicle
    if (typeof parkVehicle === "undefined" || !parkVehicle) {
        let vehicleW = px(15);
        let vehicleH = px(30);
        let vehicleX = width / 2;
        let vehicleY = height - vehicleH;
        // You can set the image here, e.g. parkCarImg or a specific one from q
        parkVehicle = new Vehicle(
            parkCarImg,
            vehicleX,
            vehicleY,
            vehicleW,
            vehicleH,
            parkTargets[q.Target - 1], // Target is 1-based
            1
        );
    }

    for (let tIndex = 0; tIndex < parkTargets.length; tIndex++) {
        let t = parkTargets[tIndex];
        let shouldBlink = false;
        if (
            blinkTargetIndex === tIndex &&
            millis() - blinkStartTime < blinkDuration &&
            Math.floor((millis() - blinkStartTime) / blinkInterval) % 2 === 0
        ) {
            shouldBlink = true;
        }
        if (shouldBlink) {
            noFill();
            stroke(255, 0, 0); // Red blinking
            strokeWeight(8);
        } else {
            noFill();
            stroke(255);
            strokeWeight(5);
        }
        rect(t.x, t.y, t.width, t.height);
    }

    // Optionally, reset blinking after duration
    if (blinkTargetIndex !== null && millis() - blinkStartTime > blinkDuration) {
        blinkTargetIndex = null;
    }

    if (typeof parkVehicle !== "undefined") {
        parkVehicle.drag();
        parkVehicle.draw();
    }
}

function getCachedImage(url) {
    return url ? imageCache[url] || null : null;
}

function loadSignForCurrentQuestion() {
    const q = questions[currentIndex];
    signTrafficSign = null;
    signSpeed = null;
    signTimerStart = null;
    signTimerActive = false;
    signTimerDone = false;
    if (q.Type === "sign" && q.signImageUrl) {
        loadImage(q.signImageUrl, img => {
            signTrafficSign = new GrowingSign(img);
            signTimerStart = millis();
            signTimerActive = true;
            signTimerDone = false;
        });
        signSpeed = q.signSpeed;
    }
    if (slider) slider.value(10); 
}

function loadPriorForCurrentQuestion() {
    priorGameInitialized = false;
    priorVehicles = [];
    priorTargets = [];
    priorSigns = [];
    placementOrder = [];
    feedbackLight = null;
    // Unlock all vehicles for the new question
    for (let v of priorVehicles) {
        v.locked = false;
    }
}

function nextQuestion() {
    // Award points before moving to the next question
    let wasCorrect = feedbackLight === 'green';
    if (wasCorrect) {
        playerScore += 10;
    }
    // Send game type and correctness to update_score.php
    fetch('update_score.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'user_id=' + window.user_id +
              '&points=' + (wasCorrect ? 10 : 0) +
              '&game=' + encodeURIComponent(game) +
              '&correct=' + (wasCorrect ? 1 : 0)
    });

    fetch('vragen.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'update_progress=1&user_id=' + window.user_id + '&progress=' + (currentIndex + 1)
    });

    currentIndex++;
    showGreenCheck = false; // <-- Clear the checkmark here
    showQuestion(currentIndex);
    updateProgressCircles(currentIndex);
    loadSignForCurrentQuestion();
    setTimeout(() => updateActiveIndicator(currentIndex), 0);
}

function updateProgressCircles(progress) {
  const circles = document.querySelectorAll('.progress-circles .circle');
  circles.forEach((circle, idx) => {
    circle.classList.remove('filled');
    if (idx < progress) {
      circle.classList.add('filled');
      circle.style.background = "#E0B44A";
      circle.style.borderColor = "#E0B44A";
    } else {
      circle.style.background = "#fff";
      circle.style.borderColor = "#E0B44A";
    }
  });
}

function updateActiveIndicator(activeIdx) {
  const circles = document.querySelectorAll('.progress-circles .circle');
  const indicator = document.getElementById('active-indicator');
  if (!indicator || !circles[activeIdx]) return;

  // Get the active circle's position relative to the viewport
  const circleRect = circles[activeIdx].getBoundingClientRect();
  const containerRect = circles[activeIdx].parentElement.getBoundingClientRect();

  // Calculate the position of the active circle's center relative to the container
  const center = circleRect.left + circleRect.width / 2 - containerRect.left;

  // Set the indicator's left so it's centered under the active circle
  indicator.style.left = `${center}px`;
}



function drawGreenCheckmark(x, y, size) {
    push();
    // Draw semi-transparent green background (slightly bigger)
    let bgSize = size * 1.2; // 20% bigger than checkmark
    noStroke();
    fill(0, 200, 0, 100); // 50% opacity green
    rectMode(CENTER);
    rect(x, y, bgSize, bgSize, 8);

    // Draw the checkmark
    stroke(0, 200, 0);
    strokeWeight(size * 0.15);
    noFill();
    beginShape();
    vertex(x - size * 0.25, y);
    vertex(x - size * 0.05, y + size * 0.25);
    vertex(x + size * 0.3, y - size * 0.2);
    endShape();
    pop();
}

function drawRedX(x, y, size) {
    push();
    // Draw semi-transparent red background (slightly bigger)
    let bgSize = size * 1.2;
    noStroke();
    fill(255, 0, 0, 100); // 50% opacity red
    rectMode(CENTER);
    rect(x, y, bgSize, bgSize, 8);

    // Draw the red X
    stroke(255, 0, 0);
    strokeWeight(size * 0.15);
    noFill();
    let offset = size * 0.25;
    line(x - offset, y - offset, x + offset, y + offset);
    line(x - offset, y + offset, x + offset, y - offset);
    pop();
}

function showQuestion(index) {
    if (!questions || !questions[index]) return; // Prevents undefined access
    document.getElementById('question-text').textContent = questions[index].Question_Text;
    game = questions[index].Type;
    if (game === "sign" && typeof loadSignForCurrentQuestion === "function") {
        loadSignForCurrentQuestion();
    }
    if (game === "prior" && typeof loadPriorForCurrentQuestion === "function") {
        loadPriorForCurrentQuestion();
    }
    updateNextBtnHref();
}

function updateNextBtnHref() {
    const nextBtn = document.getElementById('next-btn');
    if (currentIndex >= questions.length - 1) {
        nextBtn.setAttribute('href', 'resultaten.php');
    } else {
        nextBtn.setAttribute('href', '#');
    }
}

function updateActiveIndicator(activeIdx) {
    const circles = document.querySelectorAll('.progress-circles .circle');
    const indicator = document.getElementById('active-indicator');
    if (!circles[activeIdx] || !indicator) return;
    const circleRect = circles[activeIdx].getBoundingClientRect();
    const scrollLeft = window.scrollX || window.pageXOffset;
    indicator.style.left = (circleRect.left + circleRect.width / 2 + scrollLeft) + 'px';
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('next-btn').addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof currentIndex === 'undefined') {
            currentIndex = window.progress || 0;
        }
        if (currentIndex >= questions.length - 1) {
            window.location.href = 'resultaten.php';
        } else {
            currentIndex++;
            //showQuestion(currentIndex);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
  // Only set up the next button handler here
  var nextBtn = document.getElementById('next-btn');
  if (nextBtn) {
    nextBtn.onclick = function(e) {
      e.preventDefault();
      showGreenCheck = false;
      if (currentIndex < questions.length) {
        nextQuestion();
      } else {
        window.location.href = 'resultaten.php';
      }
    };
  }
});

