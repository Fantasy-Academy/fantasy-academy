export interface LabelHandlerProps {
    duration: number; // v minutách
    isCompleted: boolean;
}

function formatDuration(totalMinutes: number): string {
    if (totalMinutes <= 0) return "TIME'S UP";

    const days = Math.floor(totalMinutes / (60 * 24));
    const hours = Math.floor((totalMinutes % (60 * 24)) / 60);
    const minutes = totalMinutes % 60;

    const parts: string[] = [];

    if (days > 0) {
        parts.push(`${days}d`);
        parts.push(`${hours}h`);
    } else {
        if (hours > 0) parts.push(`${hours}h`);
        if (minutes > 0) parts.push(`${minutes}m`);
    }

    return parts.join(' ');
}

export const labelHandler = ({ duration, isCompleted }: LabelHandlerProps): string => {
    if (isCompleted) {
        return 'COMPLETED';
    } else if (duration > 0) {
        return formatDuration(duration);
    } else {
        return "TIME'S UP";
    }
};