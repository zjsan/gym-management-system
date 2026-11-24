<template>
    <div class="space-y-5">
        <!-- Top Action & Search Header Bar -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3"
        >
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <Search
                    class="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                />
                <Input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by name, membership ID, or email..."
                    class="pl-9 pr-8 h-9 bg-white border-zinc-200/90 text-xs text-[#0F1115] placeholder:text-zinc-400 shadow-2xs focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                />
                <button
                    v-if="searchQuery || memberStore.statusFilter"
                    type="button"
                    @click="handleResetFilters"
                    title="Clear search and filters"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-[#0F1115] transition-colors cursor-pointer"
                >
                    <X class="w-3.5 h-3.5" />
                </button>
            </div>

            <!-- Primary CTA: Open Register Member Modal -->
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <Button
                    v-if="searchQuery || memberStore.statusFilter"
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="handleResetFilters"
                    class="h-9 px-3 rounded-lg border-zinc-200/90 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-600 hover:text-[#0F1115] gap-1.5 cursor-pointer"
                >
                    <RotateCcw class="w-3.5 h-3.5 text-zinc-500" />
                    <span>Reset</span>
                </Button>

                <Button
                    type="button"
                    @click="openCreateModal"
                    class="h-9 px-3.5 rounded-lg bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.99] text-white text-xs font-bold shadow-sm shadow-[#F8313B]/20 gap-1.5 transition-all cursor-pointer"
                >
                    <UserPlus class="w-3.5 h-3.5 shrink-0" />
                    <span>Register Member</span>
                </Button>
            </div>
        </div>

        <!-- Full-Width Member Directory Card (Stripe / Linear Table Pattern) -->
        <Card
            class="bg-white border-zinc-200/80 shadow-2xs rounded-xl overflow-hidden"
        >
            <!-- Segmented Status Filter Bar + Total Count -->
            <div
                class="px-4 sm:px-5 py-3 border-b border-zinc-200/80 bg-zinc-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
            >
                <div
                    role="tablist"
                    aria-label="Filter members by status"
                    class="inline-flex items-center p-1 rounded-lg bg-zinc-100/90 border border-zinc-200/70 self-start overflow-x-auto max-w-full"
                >
                    <button
                        v-for="tab in statusTabs"
                        :key="tab.value"
                        type="button"
                        role="tab"
                        :aria-selected="memberStore.statusFilter === tab.value"
                        @click="selectStatusTab(tab.value)"
                        :class="[
                            'px-2.5 py-1 rounded-md text-xs font-semibold transition-all whitespace-nowrap cursor-pointer',
                            memberStore.statusFilter === tab.value
                                ? 'bg-white text-[#0F1115] shadow-2xs ring-1 ring-zinc-200/80'
                                : 'text-zinc-500 hover:text-[#0F1115]',
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <div class="flex items-center gap-2 text-xs text-zinc-500">
                    <span class="tabular-nums font-medium">
                        <strong class="font-semibold text-[#0F1115]">{{
                            totalItems
                        }}</strong>
                        {{ totalItems === 1 ? "member" : "members" }}
                    </span>
                </div>
            </div>

            <!-- Spacious Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead
                        class="bg-zinc-50/70 text-[11px] font-semibold uppercase tracking-wider text-zinc-500 border-b border-zinc-200/80"
                    >
                        <tr>
                            <th class="py-3 px-5">Member</th>
                            <th class="py-3 px-4 hidden md:table-cell">
                                Contact
                            </th>
                            <th
                                class="py-3 px-4 text-center hidden sm:table-cell"
                            >
                                Age
                            </th>
                            <th class="py-3 px-4">Validity Period</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100">
                        <!-- Loading Skeletons -->
                        <template
                            v-if="
                                memberStore.loading &&
                                !memberStore.members.length
                            "
                        >
                            <tr v-for="n in 6" :key="n">
                                <td colspan="6" class="py-3.5 px-5">
                                    <div
                                        class="flex items-center justify-between gap-4"
                                    >
                                        <div class="flex items-center gap-3">
                                            <Skeleton
                                                class="w-9 h-9 rounded-full bg-zinc-100"
                                            />
                                            <div class="space-y-1.5">
                                                <Skeleton
                                                    class="h-3.5 w-36 bg-zinc-100"
                                                />
                                                <Skeleton
                                                    class="h-3 w-24 bg-zinc-100"
                                                />
                                            </div>
                                        </div>
                                        <Skeleton
                                            class="h-3.5 w-28 bg-zinc-100 hidden md:block"
                                        />
                                        <Skeleton
                                            class="h-4 w-28 bg-zinc-100"
                                        />
                                        <Skeleton
                                            class="h-6 w-20 bg-zinc-100"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr v-else-if="memberStore.members.length === 0">
                            <td colspan="6" class="py-14 px-5 text-center">
                                <div
                                    class="max-w-xs mx-auto flex flex-col items-center"
                                >
                                    <div
                                        class="w-10 h-10 rounded-xl bg-zinc-100 text-zinc-500 flex items-center justify-center mb-3"
                                    >
                                        <Users class="w-5 h-5" />
                                    </div>
                                    <p class="text-xs font-bold text-[#0F1115]">
                                        No members found
                                    </p>
                                    <p
                                        class="text-[11px] text-zinc-500 mt-1 leading-relaxed"
                                    >
                                        Try adjusting your search or status
                                        filter, or register a new gym member.
                                    </p>
                                    <Button
                                        type="button"
                                        size="sm"
                                        @click="openCreateModal"
                                        class="mt-4 h-8 px-3 rounded-lg bg-[#0F1115] hover:bg-zinc-800 text-white text-xs font-semibold gap-1.5 cursor-pointer"
                                    >
                                        <UserPlus class="w-3.5 h-3.5" />
                                        <span>Register Member</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>

                        <!-- Member Rows -->
                        <tr
                            v-for="member in memberStore.members"
                            :key="member.id"
                            class="group hover:bg-zinc-50/80 transition-colors"
                        >
                            <!-- Member Identity -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img
                                        :src="member.photo_url"
                                        :alt="`${member.first_name} ${member.last_name}`"
                                        class="w-9 h-9 rounded-full border border-zinc-200/80 object-cover bg-zinc-100 shrink-0"
                                    />
                                    <div class="min-w-0">
                                        <p
                                            class="font-semibold text-[#0F1115] truncate leading-tight"
                                        >
                                            {{ member.first_name }}
                                            {{ member.last_name }}
                                        </p>
                                        <div
                                            class="flex items-center gap-1.5 mt-0.5"
                                        >
                                            <span
                                                class="font-mono text-[11px] font-medium text-zinc-500 tabular-nums"
                                            >
                                                {{ member.membership_no }}
                                            </span>
                                            <span class="text-zinc-300"
                                                >&middot;</span
                                            >
                                            <span
                                                class="text-[11px] text-zinc-400 capitalize"
                                            >
                                                {{ member.gender }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info (Unlocked by Full-Width Layout) -->
                            <td class="py-3.5 px-4 hidden md:table-cell">
                                <p
                                    class="text-xs font-medium text-[#0F1115] tabular-nums"
                                >
                                    {{ member.contact_number || "—" }}
                                </p>
                                <p
                                    class="text-[11px] text-zinc-500 truncate max-w-[190px] mt-0.5"
                                >
                                    {{ member.email || "No email" }}
                                </p>
                            </td>

                            <!-- Age -->
                            <td
                                class="py-3.5 px-4 text-center text-zinc-600 tabular-nums font-medium hidden sm:table-cell"
                            >
                                {{ member.age ?? "—" }}
                            </td>

                            <!-- Membership Window -->
                            <td class="py-3.5 px-4 text-[11px]">
                                <div
                                    class="font-semibold tabular-nums flex items-center gap-1.5"
                                    :class="
                                        isExpired(member.membership_end)
                                            ? 'text-[#F8313B]'
                                            : 'text-[#0F1115]'
                                    "
                                >
                                    <span>{{ member.membership_end }}</span>
                                </div>
                                <div class="text-zinc-400 tabular-nums mt-0.5">
                                    Started {{ member.membership_start }}
                                </div>
                            </td>

                            <!-- Status Pill -->
                            <td class="py-3.5 px-4 text-center">
                                <span
                                    :class="[
                                        'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border select-none',
                                        member.is_active
                                            ? 'bg-emerald-50/80 text-emerald-700 border-emerald-200/80'
                                            : 'bg-[#F8313B]/[0.07] text-[#F8313B] border-[#F8313B]/20',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'w-1.5 h-1.5 rounded-full',
                                            member.is_active
                                                ? 'bg-emerald-500'
                                                : 'bg-[#F8313B]',
                                        ]"
                                    />
                                    <span>{{
                                        member.is_active ? "Active" : "Expired"
                                    }}</span>
                                </span>
                            </td>

                            <!-- High-Craft SaaS Actions (Primary + Overflow Menu) -->
                            <td class="py-3.5 px-5 text-right">
                                <div
                                    class="inline-flex items-center justify-end gap-1.5"
                                >
                                    <!-- Contextual Renew Action (Only prominent when eligible) -->
                                    <Button
                                        v-if="member.can_renew"
                                        type="button"
                                        size="sm"
                                        @click="handleRenew(member.id)"
                                        class="h-7 px-2.5 rounded-md bg-[#F8313B]/10 hover:bg-[#F8313B] text-[#F8313B] hover:text-white text-[11px] font-bold transition-colors cursor-pointer"
                                    >
                                        Renew
                                    </Button>

                                    <!-- Quick QR Pass Trigger -->
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="openQrModal(member)"
                                        title="View or print member QR pass"
                                        class="h-7 px-2.5 rounded-md border-zinc-200/90 bg-white hover:bg-zinc-50 text-[11px] font-semibold text-[#0F1115] gap-1 cursor-pointer"
                                    >
                                        <QrCode
                                            class="w-3.5 h-3.5 text-[#F8313B]"
                                        />
                                        <span class="hidden sm:inline"
                                            >QR Pass</span
                                        >
                                    </Button>

                                    <!-- Secondary Actions Dropdown (Linear / Vercel Pattern) -->
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                aria-label="More member actions"
                                                class="h-7 w-7 p-0 rounded-md text-zinc-500 hover:text-[#0F1115] hover:bg-zinc-100 cursor-pointer"
                                            >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </Button>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuContent
                                            align="end"
                                            class="w-48 bg-white border-zinc-200/90 shadow-lg rounded-xl p-1"
                                        >
                                            <DropdownMenuItem
                                                @click="openEditModal(member)"
                                                class="text-xs font-medium text-[#0F1115] rounded-lg px-2.5 py-2 gap-2 cursor-pointer"
                                            >
                                                <Pencil
                                                    class="w-3.5 h-3.5 text-zinc-500"
                                                />
                                                <span>Edit Profile</span>
                                            </DropdownMenuItem>

                                            <DropdownMenuItem
                                                @click="openQrModal(member)"
                                                class="text-xs font-medium text-[#0F1115] rounded-lg px-2.5 py-2 gap-2 cursor-pointer"
                                            >
                                                <QrCode
                                                    class="w-3.5 h-3.5 text-zinc-500"
                                                />
                                                <span>View QR Badge</span>
                                            </DropdownMenuItem>

                                            <DropdownMenuSeparator
                                                class="bg-zinc-100 my-1"
                                            />

                                            <DropdownMenuLabel
                                                class="px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
                                            >
                                                Validity Adjustments
                                            </DropdownMenuLabel>

                                            <DropdownMenuItem
                                                @click="
                                                    handleDayAdjustment(
                                                        member.id,
                                                        1,
                                                    )
                                                "
                                                class="text-xs font-medium text-[#0F1115] rounded-lg px-2.5 py-2 gap-2 cursor-pointer"
                                            >
                                                <CalendarPlus
                                                    class="w-3.5 h-3.5 text-emerald-600"
                                                />
                                                <span>Add 1 Day (+1d)</span>
                                            </DropdownMenuItem>

                                            <DropdownMenuItem
                                                @click="
                                                    handleDayAdjustment(
                                                        member.id,
                                                        -1,
                                                    )
                                                "
                                                class="text-xs font-medium text-[#F8313B] focus:text-[#F8313B] rounded-lg px-2.5 py-2 gap-2 cursor-pointer"
                                            >
                                                <CalendarMinus
                                                    class="w-3.5 h-3.5"
                                                />
                                                <span
                                                    >Subtract 1 Day (-1d)</span
                                                >
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div
                class="flex flex-col sm:flex-row items-center justify-between px-5 py-3.5 bg-zinc-50/60 border-t border-zinc-200/80 gap-3"
            >
                <div
                    class="text-[11px] text-zinc-500 font-medium tabular-nums order-2 sm:order-1"
                >
                    Showing
                    <span class="text-[#0F1115] font-semibold">{{
                        rangeStart
                    }}</span>
                    to
                    <span class="text-[#0F1115] font-semibold">{{
                        rangeEnd
                    }}</span>
                    of
                    <span class="text-[#0F1115] font-semibold">{{
                        totalItems
                    }}</span>
                    entries
                </div>

                <div
                    class="flex items-center gap-1.5 order-1 sm:order-2 w-full sm:w-auto justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="prevPage"
                        :disabled="currentPage === 1 || memberStore.loading"
                        class="h-8 px-2.5 rounded-lg border-zinc-200/90 bg-white text-xs font-semibold text-zinc-700 gap-1 cursor-pointer"
                    >
                        <ChevronLeft class="w-3.5 h-3.5" />
                        <span>Prev</span>
                    </Button>

                    <div class="hidden md:flex items-center gap-1">
                        <button
                            v-if="visiblePages[0] > 1"
                            type="button"
                            @click="goToPage(1)"
                            :disabled="memberStore.loading"
                            class="w-8 h-8 rounded-lg text-xs font-semibold border bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50 disabled:opacity-50 tabular-nums cursor-pointer"
                        >
                            1
                        </button>

                        <span
                            v-if="visiblePages[0] > 2"
                            class="text-zinc-400 text-xs px-1"
                            >...</span
                        >

                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            type="button"
                            @click="goToPage(page)"
                            :disabled="memberStore.loading"
                            :class="[
                                'w-8 h-8 rounded-lg text-xs font-semibold border transition-colors tabular-nums cursor-pointer',
                                currentPage === page
                                    ? 'bg-[#F8313B] text-white border-[#F8313B]'
                                    : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50 disabled:opacity-50',
                            ]"
                        >
                            {{ page }}
                        </button>

                        <span
                            v-if="
                                visiblePages[visiblePages.length - 1] <
                                lastPage - 1
                            "
                            class="text-zinc-400 text-xs px-1"
                            >...</span
                        >

                        <button
                            v-if="
                                visiblePages[visiblePages.length - 1] < lastPage
                            "
                            type="button"
                            @click="goToPage(lastPage)"
                            :disabled="memberStore.loading"
                            class="w-8 h-8 rounded-lg text-xs font-semibold border bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50 disabled:opacity-50 tabular-nums cursor-pointer"
                        >
                            {{ lastPage }}
                        </button>
                    </div>

                    <span
                        class="text-xs font-medium text-zinc-500 md:hidden px-2 tabular-nums"
                    >
                        Page {{ currentPage }} of {{ lastPage }}
                    </span>

                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="nextPage"
                        :disabled="
                            currentPage === lastPage || memberStore.loading
                        "
                        class="h-8 px-2.5 rounded-lg border-zinc-200/90 bg-white text-xs font-semibold text-zinc-700 gap-1 cursor-pointer"
                    >
                        <span>Next</span>
                        <ChevronRight class="w-3.5 h-3.5" />
                    </Button>
                </div>
            </div>
        </Card>

        <!-- Register / Edit Member Shadcn Dialog Modal -->
        <Dialog :open="isMemberModalOpen" @update:open="handleModalOpenChange">
            <DialogContent
                class="sm:max-w-[520px] bg-white border border-zinc-200/90 rounded-xl p-0 shadow-xl overflow-hidden"
            >
                <DialogHeader
                    class="p-5 pb-4 border-b border-zinc-100 text-left"
                >
                    <div class="flex items-center gap-2.5">
                        <div
                            :class="[
                                'w-8 h-8 rounded-lg flex items-center justify-center shrink-0',
                                isEditing
                                    ? 'bg-amber-50 text-amber-700 border border-amber-200/70'
                                    : 'bg-[#F8313B]/10 text-[#F8313B]',
                            ]"
                        >
                            <UserCog v-if="isEditing" class="w-4 h-4" />
                            <UserPlus v-else class="w-4 h-4" />
                        </div>
                        <div>
                            <DialogTitle
                                class="text-sm font-bold tracking-tight text-[#0F1115]"
                            >
                                {{
                                    isEditing
                                        ? "Edit Member Profile"
                                        : "Register New Member"
                                }}
                            </DialogTitle>
                            <DialogDescription
                                class="text-xs text-zinc-500 mt-0.5"
                            >
                                {{
                                    isEditing
                                        ? "Update personal details, emergency contact, or photo."
                                        : "Create a member profile and generate their QR check-in pass."
                                }}
                            </DialogDescription>
                        </div>
                    </div>
                </DialogHeader>

                <form @submit.prevent="handleModalSubmit" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-semibold text-zinc-600"
                            >
                                First Name
                            </label>
                            <Input
                                v-model="memberForm.first_name"
                                placeholder="Juan"
                                required
                                class="h-9 text-xs border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                            />
                            <p
                                v-if="memberStore.errors?.first_name"
                                class="text-[11px] font-medium text-[#F8313B]"
                            >
                                {{ memberStore.errors.first_name[0] }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-semibold text-zinc-600"
                            >
                                Last Name
                            </label>
                            <Input
                                v-model="memberForm.last_name"
                                placeholder="Dela Cruz"
                                required
                                class="h-9 text-xs border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                            />
                            <p
                                v-if="memberStore.errors?.last_name"
                                class="text-[11px] font-medium text-[#F8313B]"
                            >
                                {{ memberStore.errors.last_name[0] }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-semibold text-zinc-600"
                            >
                                Contact Number
                            </label>
                            <Input
                                v-model="memberForm.contact_number"
                                placeholder="09XX XXX XXXX"
                                required
                                class="h-9 text-xs tabular-nums border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                            />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-semibold text-zinc-600"
                            >
                                Emergency Contact
                            </label>
                            <Input
                                v-model="memberForm.emergency_contact_number"
                                placeholder="09XX XXX XXXX"
                                required
                                class="h-9 text-xs tabular-nums border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                            />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Email Address
                        </label>
                        <Input
                            v-model="memberForm.email"
                            type="email"
                            placeholder="member@example.com"
                            required
                            class="h-9 text-xs border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                    </div>

                    <div class="space-y-1">
                        <label
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Residential Address
                        </label>
                        <Input
                            v-model="memberForm.address"
                            placeholder="Barangay, City / Municipality"
                            required
                            class="h-9 text-xs border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-semibold text-zinc-600"
                            >
                                Date of Birth
                            </label>
                            <Input
                                type="date"
                                v-model="memberForm.date_of_birth"
                                required
                                class="h-9 text-xs text-[#0F1115] border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                            />
                            <p
                                v-if="memberStore.errors?.date_of_birth"
                                class="text-[11px] font-medium text-[#F8313B]"
                            >
                                {{ memberStore.errors.date_of_birth[0] }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-semibold text-zinc-600"
                            >
                                Gender
                            </label>
                            <select
                                v-model="memberForm.gender"
                                required
                                class="w-full h-9 px-3 rounded-lg bg-white border border-zinc-200/90 text-xs text-[#0F1115] focus:outline-none focus:ring-2 focus:ring-[#F8313B]/20 focus:border-[#F8313B]"
                            >
                                <option disabled value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Member Photo Profile
                        </label>
                        <input
                            type="file"
                            @change="handleFileUpload"
                            accept="image/*"
                            class="w-full text-[11px] text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-zinc-200 file:text-[11px] file:font-semibold file:bg-zinc-50 file:text-[#0F1115] hover:file:bg-zinc-100 cursor-pointer"
                        />
                    </div>

                    <DialogFooter
                        class="pt-3 border-t border-zinc-100 flex flex-row justify-end gap-2"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            @click="handleModalOpenChange(false)"
                            :disabled="memberStore.loading"
                            class="h-9 px-3.5 rounded-lg border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-700 cursor-pointer"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="memberStore.loading"
                            class="h-9 px-4 rounded-lg bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.98] text-white text-xs font-bold shadow-sm shadow-[#F8313B]/20 transition-all cursor-pointer"
                        >
                            {{
                                memberStore.loading
                                    ? "Saving..."
                                    : isEditing
                                      ? "Save Changes"
                                      : "Complete Registration"
                            }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- QR Badge Modal -->
        <QrBadge
            v-model="showQrModal"
            :member="selectedQrMember"
            :qr-data="memberStore.qrData"
            :is-loading="memberStore.qrLoading"
            :is-emailing="isEmailingQr"
            @print="printQrBadge"
            @email="handleSendQrEmail"
            @regenerate="handleRegenerateQr"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from "vue";
import { useMemberStore } from "@/stores/memberStore";
import debounce from "lodash.debounce";
import { usePagination } from "@/composables/usePagination";
import { storeToRefs } from "pinia";
import { useRouter, useRoute } from "vue-router";
import { Input } from "@/components/ui/input";
import QrBadge from "../Components/QrBadge.vue";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    Search,
    X,
    RotateCcw,
    UserPlus,
    UserCog,
    Users,
    QrCode,
    MoreHorizontal,
    Pencil,
    CalendarPlus,
    CalendarMinus,
    ChevronLeft,
    ChevronRight,
} from "lucide-vue-next";

//navigation
const router = useRouter();
const route = useRoute();

const goToAttendance = () => {
    router.push({ name: "attendance" });
};

const goToPaymentHistory = () => {
    router.push({ name: "payment" });
};

const goToDashboard = () => {
    router.push({ name: "DashboardView" });
};

const goToUserProfile = () => {
    router.push({ name: "UserProfile" });
};

// Local states
const memberStore = useMemberStore();
const isEditing = ref(false);
const currentMemberId = ref(null);
const searchQuery = ref("");
const isMemberModalOpen = ref(false);

//  QR Badge Modal States
const showQrModal = ref(false);
const selectedQrMember = ref(null);

// Component-level feedback states
const successMessage = ref("");
const errorMessage = ref("");
const modalErrorMessage = ref("");

const isEmailingQr = ref(false);

//status filtering function
const onFilterChange = () => {
    memberStore.setStatusFilter(memberStore.statusFilter);
};

const handleResetFilters = () => {
    memberStore.resetFilters();
    searchQuery.value = "";
};

// ---------------------------------------------------
// Search and Pagination Logic
// ---------------------------------------------------
const loadPage = async (pageNumber, searchKeyword = searchQuery.value) => {
    try {
        errorMessage.value = "";
        await memberStore.fetchMembers(
            pageNumber,
            memberStore.itemsPerPage,
            searchKeyword,
        );
    } catch (err) {
        errorMessage.value =
            err?.response?.data?.message ||
            err?.message ||
            "Failed to load registry.";
    }
};

// Custom pagination composable setup
const {
    currentPage,
    lastPage,
    totalItems,
    isLoading,
    visiblePages,
    rangeStart,
    rangeEnd,
    prevPage,
    nextPage,
    goToPage,
} = usePagination(memberStore, loadPage);

const debouncedSearch = debounce((targetQuery) => {
    loadPage(1, targetQuery);
}, 500);

watch(searchQuery, (newVal, oldVal) => {
    const currentText = newVal?.trim() || "";
    const previousText = oldVal?.trim() || "";

    if (currentText === previousText) {
        return;
    }

    debouncedSearch(currentText);
});

const statusTabs = [
    { label: "All", value: "" },
    { label: "Active", value: "active" },
    { label: "Expiring Soon", value: "expiring_soon" },
    { label: "Expired", value: "expired" },
];

const selectStatusTab = (value) => {
    memberStore.statusFilter = value;
    onFilterChange();
};

const openCreateModal = () => {
    resetForm();
    isMemberModalOpen.value = true;
};

const openEditModal = (member) => {
    editMember(member);
    isMemberModalOpen.value = true;
};

const handleModalOpenChange = (open) => {
    isMemberModalOpen.value = open;
    if (!open) {
        resetForm();
    }
};

const handleModalSubmit = async () => {
    await submitForm();
    // Close modal automatically when validation succeeds (no errors in store)
    if (!memberStore.errors || Object.keys(memberStore.errors).length === 0) {
        isMemberModalOpen.value = false;
    }
};

// ---------------------------------------------------
// Mobile Number Validation
// ---------------------------------------------------
const phMobileRegex = /^(?:\+63|63|0)?9\d{9}$/;

const isContactNumberValid = computed(() => {
    return phMobileRegex.test(memberForm.value.contact_number || "");
});

const isEmergencyContactValid = computed(() => {
    return phMobileRegex.test(memberForm.value.emergency_contact_number || "");
});

const isContactInfoValid = computed(() => {
    return isContactNumberValid.value && isEmergencyContactValid.value;
});

// email format validation using standard email validation regex pattern
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

//  Computed validation flag
const isEmailInvalid = computed(() => {
    // If the input is empty, let required handle it
    if (!memberForm.value.email) return false;

    // Returns true if the email fails the regex test
    return !emailRegex.test(memberForm.value.email);
});

// ---------------------------------------------------
// Form States
// ---------------------------------------------------
const initialState = {
    first_name: "",
    last_name: "",
    contact_number: "",
    emergency_contact_number: "",
    email: "",
    address: "",
    date_of_birth: "",
    gender: "",
    photo: null,
};

const memberForm = ref({ ...initialState });

const resetForm = () => {
    isEditing.value = false;
    currentMemberId.value = null;
    memberForm.value = { ...initialState };
    memberStore.errors = null;
};

const editMember = (member) => {
    isEditing.value = true;
    currentMemberId.value = member.id;

    const rawBirthDate = member.date_of_birth
        ? member.date_of_birth.substring(0, 10)
        : "";

    memberForm.value = {
        first_name: member.first_name,
        last_name: member.last_name,
        contact_number: member.contact_number,
        emergency_contact_number: member.emergency_contact_number,
        email: member.email,
        address: member.address,
        date_of_birth: rawBirthDate,
        gender: member.gender,
        photo: null,
    };
};

// ---------------------------------------------------
// Photo Handling
// ---------------------------------------------------
const handleFileUpload = (event) => {
    if (event.target.files.length > 0) {
        memberForm.value.photo = event.target.files[0];
    }
};

// ---------------------------------------------------
// Form Submission
// ---------------------------------------------------
const submitForm = async () => {
    if (!isContactInfoValid.value) {
        alert("Please provide valid Philippine mobile numbers.");
        return;
    }

    const data = new FormData();
    Object.keys(memberForm.value).forEach((key) => {
        if (key !== "photo" && memberForm.value[key] !== null) {
            data.append(key, memberForm.value[key]);
        }
    });

    if (memberForm.value.photo instanceof File) {
        data.append("photo", memberForm.value.photo);
    }

    try {
        let result;
        if (isEditing.value) {
            // Laravel-friendly spoofing fallback method for multipart PUT configurations
            data.append("_method", "PUT");
            result = await memberStore.updateMember(
                currentMemberId.value,
                data,
            );
        } else {
            result = await memberStore.addMember(data);
        }

        if (result && result.success) {
            successMessage.value = isEditing.value
                ? "Member updated successfully!"
                : "Member added successfully!";
            resetForm();
            loadPage(currentPage.value); // Hot-reload current dataset view
        } else {
            alert(
                result?.message ||
                    "Action failed. Double-check backend validation schemas.",
            );
        }
    } catch (error) {
        console.error("Form submission failed:", error);
        const errMsg =
            error.response?.data?.message ||
            "A network error occurred. Please try again later.";
        alert(errMsg);
    }
};

// ---------------------------------------------------
// Member Additional Operations
// ---------------------------------------------------

const handleRenew = async (id) => {
    if (confirm("Confirm membership extension by 30 days?")) {
        const res = await memberStore.renewMember(id);
        if (!res.success) {
            alert(res.message);
        }
    }
};

const handleDayAdjustment = async (id, days) => {
    const res = await memberStore.adjustMemberDays(id, days);
    if (!res.success) alert(res.message);
};

const isExpired = (endDateString) => {
    if (!endDateString) return false;
    return new Date() > new Date(endDateString);
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date.getTime())
        ? "N/A"
        : date.toLocaleDateString(undefined, {
              year: "numeric",
              month: "short",
              day: "numeric",
          });
};

//  Open Modal & Fetch QR Code
const openQrModal = async (member) => {
    selectedQrMember.value = member;
    showQrModal.value = true;
    await memberStore.fetchMemberQrCode(member.id);
};

//  Close Modal & Clear State
const closeQrModal = () => {
    showQrModal.value = false;
    selectedQrMember.value = null;
    memberStore.clearQrData();
};

//  Regenerate QR Token Handler
const handleRegenerateQr = async () => {
    if (!selectedQrMember.value) return;

    const confirmed = confirm(
        "Regenerating this QR code will invalidate any existing printed pass. Proceed?",
    );
    if (!confirmed) return;

    const res = await memberStore.regenerateMemberQrToken(
        selectedQrMember.value.id,
    );
    if (!res.success) {
        alert(res.message);
    }
};

//  Print Badge Handler
const printQrBadge = () => {
    const badgeElement = document.getElementById("printable-qr-badge");
    if (!badgeElement) return;

    const printWindow = window.open("", "_blank", "width=600,height=600");
    printWindow.document.write(`
        <html>
            <head>
                <title>Print QR Badge - ${selectedQrMember.value?.first_name || "Member"}</title>
                <style>
                    body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                    .badge { text-align: center; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
                    svg { width: 200px; height: 200px; }
                </style>
            </head>
            <body>
                <div class="badge">
                    <h2>${selectedQrMember.value?.first_name} ${selectedQrMember.value?.last_name}</h2>
                    ${badgeElement.innerHTML}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
};

//QR code email sending function
const handleSendQrEmail = async () => {
    if (!selectedQrMember.value) return;

    isEmailingQr.value = true;
    const res = await memberStore.sendQrCodeEmail(selectedQrMember.value.id);
    isEmailingQr.value = false;

    if (res.success) {
        alert("QR Code email has been queued for sending.");
    } else {
        alert(res.message || "Failed to queue QR Code email.");
    }
};

// ---------------------------------------------------
// Mounting Guards
// ---------------------------------------------------
onMounted(() => {
    console.log("Component mounted, loading initial data.");
    loadPage(currentPage.value, searchQuery.value.trim() || "");

    // Catch URL query from Dashboard button
    if (route.query.filter) {
        memberStore.statusFilter = route.query.filter;
    }
});

onUnmounted(() => {
    debouncedSearch.cancel();
    if (memberStore.currentAbortController) {
        memberStore.currentAbortController.abort();
    }
});
</script>
