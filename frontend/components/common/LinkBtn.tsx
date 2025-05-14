import React from 'react';
import Link from 'next/link';

interface LinkButtonProps {
    link: string;
    text: string;
}

const LinkButton: React.FC<LinkButtonProps> = ({ link, text }) => {
    return (
        <div className="text-sm font-semibold text-charcoal underline">
            <Link href={link}>
                {text}
            </Link>
        </div>
    );
};

export default LinkButton;