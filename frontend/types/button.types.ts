export type Button = {
    type?: 'button' | 'submit' | 'reset';
    onClick?: () => void;
    text: string;
    className?: string;
    disabled?: boolean;
}