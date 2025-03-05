export type ChallengeCard = {
    id: string;
    title: string;
    description: string;
    duration: number;
    isCompleted: boolean;
    isTimeUp?: boolean;
}