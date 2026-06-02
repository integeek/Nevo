const positiveMessages = [
    { message: "You're doing great" },
    { message: "Small progress is still progress" },
    { message: "Every effort counts" },
    { message: "Keep the streak alive" },
    { message: "You're stronger than you think" },
    { message: "You made progress today" },
    { message: "Don't give up now" },
    { message: "Tiny steps, big results" },
    { message: "You're leveling up" },
];

function getRandomMessage() {
    const index = Math.floor(Math.random() * positiveMessages.length);
    return positiveMessages[index];
}

function allStepsDone(done, total) {
    return total > 0 && done === total;
}

module.exports = { positiveMessages, getRandomMessage, allStepsDone };
