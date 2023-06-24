export const getStringFromTimeObject = (timeObject) => {
    const hours = String(timeObject.hours).padStart(2, '0');
    const minutes = String(timeObject.minutes).padStart(2, '0');
    const seconds = String(timeObject.seconds).padStart(2, '0');
    return `${hours}:${minutes}:${seconds}`;
};

export const getTimeObjectFromString = (timeString) => {
    const [hours, minutes, seconds] = timeString.split(':').map(Number);
    return {hours: hours, minutes: minutes, seconds: seconds};
};
