<template>
  <div class="dashboard-editor-container">
    <panel-group :chart-data="statistics" />
    <el-row style="background:#fff;margin-bottom:32px;">
      <el-date-picker
        v-model="value2"
        type="daterange"
        align="right"
        unlink-panels
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        :picker-options="pickerOptions"
      >
      </el-date-picker>
      <el-button type="primary" icon="el-icon-search" @click="pickDate">搜索</el-button>
    </el-row>
    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
      <line-chart :chart-data="statistics" />
    </el-row>
    <el-row :gutter="32">
      <!--<el-col :xs="24" :sm="24" :lg="8">-->
        <!--<div class="chart-wrapper">-->
          <!--<raddar-chart />-->
        <!--</div>-->
      <!--</el-col>-->
      <el-col :xs="24" :sm="24" :lg="12">
        <div class="chart-wrapper">
          <pie-chart :chart-data="statistics" />
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="12">
        <div class="chart-wrapper">
          <bar-chart :chart-data="statistics" />
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { getData } from '@/api/statistics'
import PanelGroup from './components/PanelGroup'
import LineChart from './components/LineChart'
import RaddarChart from './components/RaddarChart'
import PieChart from './components/PieChart'
import BarChart from './components/BarChart'

const lineChartData = {
  newVisitis: {
    expectedData: [100, 120, 161, 134, 105, 160, 165],
    actualData: [120, 82, 91, 154, 162, 140, 145]
  }
}

export default {
  name: 'DashboardAdmin',
  components: {
    PanelGroup,
    LineChart,
    RaddarChart,
    PieChart,
    BarChart
  },

  data() {
    return {
      lineChartData: lineChartData.newVisitis,
      statistics: [],
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      },
      value2: '',
      post: {
        start: null,
        end: null
      }
    }
  },
  created() {
    const start1 = new Date()
    start1.setTime(start1.getTime() - 3600 * 1000 * 24 * 7)
    this.post.start = start1
    this.post.end = new Date()
    this.getData(this.post)
  },
  methods: {
    async getData(date) {
      const res = await getData(date)
      this.statistics = res.data
    },
    pickDate() {
      this.post.start = this.value2[0]
      this.post.end = this.value2[1]
      this.getData(this.post)
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  position: relative;

  .github-corner {
    position: absolute;
    top: 0px;
    border: 0;
    right: 0;
  }

  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}

@media (max-width:1024px) {
  .chart-wrapper {
    padding: 8px;
  }
}
</style>
