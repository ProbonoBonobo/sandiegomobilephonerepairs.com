function loadInstructions(opcode) {
    'use strict';
    var memory = ["foo",
                  "bar",
                  "baz",
                  "bees",
                  "dickbutt"];
    return memory[opcode];
}

loadInstructions(1);