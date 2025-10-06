@extends('layouts.app')

@section('title', $pageTitle ?? 'Dashboard')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Dashboard</h1>
    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Tổng quan nhanh về hệ thống</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] p-4">
      <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Tổng người dùng</div>
      <div class="text-2xl font-semibold mt-1">{{ number_format($stats['totalUsers']) }}</div>
    </div>
    <div class="rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] p-4">
      <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Đang hoạt động</div>
      <div class="text-2xl font-semibold mt-1">{{ number_format($stats['activeUsers']) }}</div>
    </div>
    <div class="rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] p-4">
      <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Mới tuần này</div>
      <div class="text-2xl font-semibold mt-1">{{ number_format($stats['newThisWeek']) }}</div>
    </div>
    <div class="rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] p-4">
      <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Tỉ lệ chuyển đổi</div>
      <div class="text-2xl font-semibold mt-1">{{ number_format($stats['conversionRate'], 2) }}%</div>
    </div>
  </div>

  <div class="rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] p-4">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-medium">Hoạt động gần đây</h2>
      <a href="#" class="text-sm underline underline-offset-4 text-[#f53003] dark:text-[#FF4433]">Xem tất cả</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-[#706f6c] dark:text-[#A1A09A] border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <th class="py-2">Thời gian</th>
            <th class="py-2">Sự kiện</th>
            <th class="py-2">Chi tiết</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="py-2">-</td>
            <td class="py-2">Chưa có dữ liệu</td>
            <td class="py-2">-</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
@endsection
