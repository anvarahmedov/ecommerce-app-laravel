import Navbar from '@/Components/App/Navbar';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode, useEffect, useRef, useState } from 'react';

export default function AuthenticatedLayout({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const props = usePage().props;
    const user = props.auth.user;

    const [successMessages, setSuccessMessages] = useState<any[]>([]);
    const timeoutRefs = useRef<{ [key:number]: ReturnType<typeof setTimeout>}>({});

    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

    useEffect(() => {
        if (props.success.message) {
            const newMessage = {
                ...props.success,
                id: props.success.time,
            };

            setSuccessMessages((prevMessage) => [newMessage, ...prevMessage]);

            const timeoutId = setTimeout(() => {
                setSuccessMessages((prevMessages) => {
                    return prevMessages.filter((msg) => msg.id !== newMessage.id);
                });
                delete timeoutRefs.current[newMessage.id]
            }, 5000);
            timeoutRefs.current[newMessage.id] = timeoutId
        }
    }, [props.success]);

    return (


        <div className="min-h-screen bg-gray-100 dark:bg-gray-900 font-trebuchet">



            <Navbar/>

            {props.error && (
                <div className='container mx-auto px-8 mt-8'>
                    <div className='alert alert-error'>
                        {props.error}
                    </div>
                </div>
            )}

            {successMessages.length > 0 && (
                <div className='toast toast-top toast-end z-[1000] mt-16'>
                    {successMessages.map(
                        (msg) => (
                            <div className='alert alert-success' key={msg.id}>
                                <span>{msg.message}</span>
                            </div>
                        )
                    )}
                </div>
            )}

                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                </div>

                <div
                    className={
                        (showingNavigationDropdown ? 'block' : 'hidden') +
                        ' sm:hidden'
                    }
                >
                    <div className="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            href={route('dashboard')}
                            active={route().current('dashboard')}
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <div className="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
                        <div className="px-4">
                            <div className="text-base font-medium text-gray-800 dark:text-gray-200">
                                {user.name}
                            </div>
                            <div className="text-sm font-medium text-gray-500">
                                {user.email}
                            </div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink href={route('profile.edit')}>
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                method="post"
                                href={route('logout')}
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>




            <main>{children}</main>
        </div>
    );
}
