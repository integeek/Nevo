const { positiveMessages, getRandomMessage, allStepsDone } = require('./homePageChildRules');

// Random positive message
test('getRandomMessage returns a valid message object', () => {
    const result = getRandomMessage();
    expect(result).toBeDefined();
    expect(result.message).toBeDefined();
});

test('getRandomMessage always returns a message from the list', () => {
    for (let i = 0; i < 50; i++) {
        const result = getRandomMessage();
        expect(positiveMessages).toContain(result);
    }
});

// All steps done
test('allStepsDone returns true when all steps are completed', () => {
    expect(allStepsDone(3, 3)).toBe(true);
});

test('allStepsDone returns false when some steps are remaining', () => {
    expect(allStepsDone(2, 3)).toBe(false);
});

test('allStepsDone returns false when no steps are done', () => {
    expect(allStepsDone(0, 3)).toBe(false);
});

test('allStepsDone returns false when there are no steps at all', () => {
    expect(allStepsDone(0, 0)).toBe(false);
});
