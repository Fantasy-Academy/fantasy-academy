import React from 'react';
import Link from 'next/link';

interface LinkButtonProps {
    link: string;
    text: string;
}

const LinkButton: React.FC<LinkButtonProps> = ({ link, text }) => {
    return (
        <div className="text-sm font-semibold text-gray-800 hover:text-gray-600 underline">
            <Link href={link}>
                {text}
            </Link>
        </div>
    );
};

export default LinkButton;