let a = 3;
let b = 9;

[a, b] = [b, a];

console.log("Swap Number:", "a=" + a, "b=" + b);

function square(n) {
    return n * n;
}

for (let i = 1; i <= 10; i++) {
    console.log("Square", i, "=", square(i));
}


let arr = [15, 18, 34, 67, 93, 45, 23, 56, 78, 12];

let lar = arr[0];

for (let i = 0; i < arr.length; i++) {
    if (arr[i] > lar) {
        lar = arr[i];
    }
}

console.log("Large Number is", lar);

