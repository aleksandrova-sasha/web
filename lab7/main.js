const display = document.querySelector(".display");
const buttons = Array.from(document.querySelectorAll(".button"));

function Clear() {
    display.innerText = "0";
}

function Equals() {
    try {
        display.innerText = eval(display.innerText);
    } catch (e) {
        display.innerText = "Error!";
    }
}

function ClearE() {
    display.innerText = "0";
}

function SignChange() {
    if (display.innerText !== "0") {
        display.innerText = (parseFloat(display.innerText) * -1).toString();
    }
}

function Percentage() {
    display.innerText = (parseFloat(display.innerText) / 100).toString();
}

function Delete() {
    display.innerText = display.innerText.slice(0, -1);
}

function Decimal() {
    if (!display.innerText.includes(".")) {
        display.innerText += ".";
    }
}

function NumberInput(number) {
    if (display.innerText == "0" && number !== ".") {
        display.innerText = number;
    } else {
        display.innerText += number;
    }
}

buttons.forEach((button) => {
    button.addEventListener("click", (e) => {
        switch (e.target.innerText) {
            case "C":
                Clear();
                break;
            case "CE":
                ClearE();
                break;
            case "=":
                Equals();
                break;
            case "+/-":
                SignChange();
                break;
            case "%":
                Percentage();
                break;
            case "DEL":
                Delete();
                break;
            case ".":
                Decimal();
                break;
            default:
                NumberInput(e.target.innerText);
        }
    });
});
