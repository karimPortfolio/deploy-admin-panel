<template>
   <q-page class="q-pa-md">
    <!-- <page-header
      title="Dashboard"
      subtitle="Welcome to your dashboard"
      icon="dashboard"
    /> -->

    <div>
      <div class="grid lg:grid-cols-3 gap-4" >
        <servers-total-card />
        <security-group-total-card />
        <ssh-keys-total-card />
      </div>
      <div class="grid lg:grid-cols-2 gap-4 mt-4" >
        <monthly-servers-total-chart-card />
        <monthly-security-group-total-chart-card />
      </div>
      <div class="grid lg:grid-cols-2 gap-4 mt-4" >
        <total-servers-by-status-chart-card />
        <total-servers-by-security-groups-chart-card />
      </div>
    </div>
   </q-page>
</template>
<script setup>
import PageHeader from '@/components/PageHeader.vue';
import ServersTotalCard from './partials/ServersTotalCard.vue';
import SecurityGroupTotalCard from './partials/SecurityGroupTotalCard.vue';
import SshKeysTotalCard from './partials/SshKeysTotalCard.vue';
import MonthlyServersTotalChartCard from './partials/MonthlyServersTotalChartCard.vue';
import MonthlySecurityGroupTotalChartCard from './partials/MonthlySecurityGroupTotalChartCard.vue';
import TotalServersByStatusChartCard from './partials/TotalServersByStatusChartCard.vue';
import TotalServersBySecurityGroupsChartCard from './partials/TotalServersBySecurityGroupsChartCard.vue';
</script>