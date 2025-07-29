export type ChallengeCard = {
    id: string;
    title: string;
    description: string;
    image: string;
    duration: number;
    isCompleted: boolean;
    isTimeUp?: boolean;
}