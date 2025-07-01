import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type User as UserType } from '@/types';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { useState, useEffect } from 'react';
import axios from 'axios';
import { UserPlus as UserPlusIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/users',
    },
];

export default function AddUser() {
    const [showAddUserForm, setShowAddUserForm] = useState(false);
    const [users, setUsers] = useState<UserType[]>([]);
    const [loadingUsers, setLoadingUsers] = useState(true);
    const [errorFetchingUsers, setErrorFetchingUsers] = useState<string | null>(null);

    const fetchUsers = async () => {
        setLoadingUsers(true);
        setErrorFetchingUsers(null);
        try {
            const response = await axios.get('/api/users');
            setUsers(response.data.data);
        } catch (error) {
            console.error("Error fetching users:", error);
            setErrorFetchingUsers("Failed to load users. Please try again later.");
        } finally {
            setLoadingUsers(false);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add Management" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 mb-4">
                    <div className="flex justify-between items-center mb-4">
                        <h2 className="text-xl font-semibold">Add New User Account</h2>
                        <Button onClick={() => setShowAddUserForm(!showAddUserForm)}>
                            <UserPlusIcon className="h-4 w-4 mr-2" />
                            {showAddUserForm ? 'Hide Form' : 'Add New User'}
                        </Button>
                    </div>
                    {/* Form akan muncul di sini jika showAddUserForm true */}
                </div>

                {/* Bagian untuk Daftar User */}
                <div className="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6">
                    <h2 className="text-xl font-semibold mb-4">Existing Users</h2>
                    {loadingUsers ? (
                        <p>Loading users...</p>
                    ) : errorFetchingUsers ? (
                        <p className="text-red-500">{errorFetchingUsers}</p>
                    ) : users.length === 0 ? (
                        <p>No users found.</p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead className="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            Username
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            Email
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            Role
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            Created At
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                    {users.map((user) => (
                                        <tr key={user.id}>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {user.username}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {user.email}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {user.role}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {user.created_at}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
