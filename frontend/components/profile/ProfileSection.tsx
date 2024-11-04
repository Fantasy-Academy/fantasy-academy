import React from 'react';

interface ProfileSectionProps {
  title: string;
  items: string[] | number;
}

const ProfileSection: React.FC<ProfileSectionProps> = ({ title, items }) => (
  <div className="mb-6">
    <h3 className="text-lg font-semibold text-gray-800">{title}</h3>
    {Array.isArray(items) ? (
      <ul className="list-disc list-inside">
        {items.map((item, index) => (
          <li key={index} className="text-gray-700">
            {item}
          </li>
        ))}
      </ul>
    ) : (
      <p className="text-gray-700">{items}</p>
    )}
  </div>
);

export default ProfileSection;