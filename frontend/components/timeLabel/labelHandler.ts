export interface LabelHandlerProps {
    duration: number;
    isCompleted: boolean;
}

export const labelHandler = ({ duration, isCompleted }: LabelHandlerProps): string | number => {
    if (duration > 0 && !isCompleted) {
        return duration;
    } else if (isCompleted) {
        return 'COMPLETED';
    } else {
        return "TIME'S UP";
    }
};