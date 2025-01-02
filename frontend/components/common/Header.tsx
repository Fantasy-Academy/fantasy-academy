'use client'; 
import React from 'react';
import { Disclosure, Menu } from '@headlessui/react';
import { usePathname } from 'next/navigation';
import Link from 'next/link';
import { useSession } from 'next-auth/react';

const navigation: { name: string; href: string }[] = [
  { name: 'Dashboard', href: '/dashboard' },
  { name: 'Challenges', href: '/challenges' },
  { name: 'Leaderboard', href: '/leaderboard' },
];

function classNames(...classes: string[]): string {
  return classes.filter(Boolean).join(' ');
}

export const Header = () => {
  const currentPath = usePathname();
  const { data: session } = useSession();

  return (
    <Disclosure as="nav" className="bg-gray-800 fixed top-0 w-full z-50 shadow-lg">
      <div className="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div className="relative flex h-16 items-center justify-between">
          <div className="absolute inset-y-0 left-0 flex items-center sm:hidden">
            {/* Mobile menu button */}
            <Disclosure.Button className="group relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
              <span className="sr-only">Open main menu</span>
              <div className="flex flex-col space-y-1">
                <div className="h-0.5 w-6 bg-gray-400 group-hover:bg-white transition duration-200"></div>
                <div className="h-0.5 w-6 bg-gray-400 group-hover:bg-white transition duration-200"></div>
                <div className="h-0.5 w-6 bg-gray-400 group-hover:bg-white transition duration-200"></div>
              </div>
            </Disclosure.Button>
          </div>
          <div className="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
            <div className="hidden sm:ml-6 sm:block">
              <div className="flex space-x-4">
                {navigation.map((item) => (
                  <Link
                    key={item.name}
                    href={item.href}
                    aria-current={item.href === currentPath ? 'page' : undefined}
                    className={classNames(
                      item.href === currentPath ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                      'rounded-md px-3 py-2 text-sm font-medium'
                    )}
                  >
                    {item.name}
                  </Link>
                ))}
              </div>
            </div>
          </div>
          <div className="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
            {/* Conditional rendering of Login or Your Profile */}
            {session ? (
              <Link
                href="/profile" // Link to user profile page
                className="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium"
              >
                Your Profile
              </Link>
            ) : (
              <Link
                href="/login" // Use Link for client-side navigation
                className="bg-gray-300 text-gray-800 hover:bg-gray-400 hover:text-white rounded-md px-3 py-2 text-sm font-medium"
              >
                Login
              </Link>
            )}

            {/* Profile dropdown - remove user image */}
            <Menu as="div" className="relative ml-3">
              <Menu.Button className="flex text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                <span className="sr-only">Open user menu</span>
                {/* Optional: Add a profile icon or placeholder here */}
              </Menu.Button>
              <Menu.Items
                className="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 transition focus:outline-none"
              >
                <Menu.Item>
                  {({ active }: { active: boolean }) => (
                    <a
                      href="#"
                      className={`block px-4 py-2 text-sm text-gray-700 ${active && 'bg-gray-100'}`}
                    >
                      Your Profile
                    </a>
                  )}
                </Menu.Item>
                <Menu.Item>
                  {({ active }: { active: boolean }) => (
                    <a
                      href="#"
                      className={`block px-4 py-2 text-sm text-gray-700 ${active && 'bg-gray-100'}`}
                    >
                      Sign out
                    </a>
                  )}
                </Menu.Item>
              </Menu.Items>
            </Menu>
          </div>
        </div>
      </div>

      <Disclosure.Panel className="sm:hidden">
        <div className="space-y-1 px-2 pb-3 pt-2">
          {navigation.map((item) => (
            <Disclosure.Button
              key={item.name}
              as="a"
              href={item.href}
              aria-current={item.href === currentPath ? 'page' : undefined}
              className={classNames(
                item.href === currentPath ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                'block rounded-md px-3 py-2 text-base font-medium'
              )}
            >
              {item.name}
            </Disclosure.Button>
          ))}
        </div>
      </Disclosure.Panel>
    </Disclosure>
  );
};

export default Header;