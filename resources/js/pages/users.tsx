import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type User as UserType } from '@/types';
import { Head, usePage, useForm, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { useState } from 'react';
import { PencilIcon, TrashIcon, UserPlus as UserPlusIcon } from 'lucide-react';
import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/users',
    },
];

export default function Index() {
    const [showDialog, setShowDialog] = useState(false);
    const { props } = usePage<{ users: { data: UserType[] }; success_message?: string }>();
    const users = props.users.data ?? [];

    const { data, setData, post, put, processing, errors, reset } = useForm({
        username: '',
        email: '',
        password: '',
        role: '',
    });

    const [editingId, setEditingId] = useState<number | null>(null);

    const handleEdit = (user: UserType) => {
        setData({
            username: user.username,
            email: user.email,
            password: '',
            role: user.role,
        });
        setEditingId(user.id);
        setShowDialog(true);
    };

    const handleDelete = (id: number) => {
        if (confirm("Are you sure you want to delete this user?")) {
            router.delete(`/users/${id}`, {
                preserveScroll: true,
            });
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editingId) {
            put(`/users/${editingId}`, {
                onSuccess: () => {
                    reset();
                    setEditingId(null);
                    setShowDialog(false);
                },
            });
        } else {
            post('/users', {
                onSuccess: () => {
                    reset();
                    setShowDialog(false);
                },
            });
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="User Management" />
            <div className="flex flex-col gap-4 p-4">
                {/* Form Add/Edit */}
                <div className="border p-6 rounded-xl">
                    <div className="flex justify-between items-center mb-4">
                        <h2 className="text-xl font-semibold">Manage User Account</h2>

                        <Dialog open={showDialog} onOpenChange={(open) => {
                            setShowDialog(open);
                            if (!open) {
                                reset();
                                setEditingId(null);
                            }
                        }}>
                            <DialogTrigger asChild>
                                <Button>
                                    <UserPlusIcon className="h-4 w-4 mr-2" />
                                    Add New User
                                </Button>
                            </DialogTrigger>

                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>{editingId ? 'Edit User' : 'Add New User'}</DialogTitle>
                                </DialogHeader>

                                <form onSubmit={handleSubmit} className="grid gap-4 py-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="username">Username</Label>
                                        <Input
                                            id="username"
                                            value={data.username}
                                            onChange={(e) => setData('username', e.target.value)}
                                            required
                                        />
                                        {errors.username && <p className="text-sm text-red-500">{errors.username}</p>}
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="email">Email</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            required
                                        />
                                        {errors.email && <p className="text-sm text-red-500">{errors.email}</p>}
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password">Password</Label>
                                        <Input
                                            id="password"
                                            type="password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            required={!editingId}
                                            placeholder={editingId ? 'Leave blank to keep current password' : ''}
                                        />
                                        {errors.password && <p className="text-sm text-red-500">{errors.password}</p>}
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="role">Role</Label>
                                        <Select
                                            value={data.role}
                                            onValueChange={(value) => setData('role', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select role" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="superadmin">Superadmin</SelectItem>
                                                <SelectItem value="admin">Admin</SelectItem>
                                                <SelectItem value="board">Board</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.role && <p className="text-sm text-red-500">{errors.role}</p>}
                                    </div>

                                    <DialogFooter>
                                        <Button type="submit" disabled={processing}>
                                            {editingId ? 'Update' : 'Submit'}
                                        </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                {/* Table Users */}
                <div className="border p-6 rounded-xl">
                    <h2 className="text-xl font-semibold mb-4">Existing Users</h2>
                    {users.length === 0 ? (
                        <p>No users found.</p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50 dark:bg-[#222831]">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Username</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Created At</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200 dark:bg-[#31363F]">
                                    {users.map((user) => (
                                        <tr key={user.id}>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">{user.username}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">{user.email}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">{user.role}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">{user.created_at}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm flex gap-1">
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    className="text-blue-600 hover:bg-blue-100 hover:dark:bg-blue-300"
                                                    onClick={() => handleEdit(user)}
                                                >
                                                    <PencilIcon className="h-3 w-3" />
                                                </Button>
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    className="text-red-600 hover:bg-red-100 hover:dark:bg-red-300"
                                                    onClick={() => handleDelete(user.id)}
                                                >
                                                    <TrashIcon className="h-3 w-3" />
                                                </Button>
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
