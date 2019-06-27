<template>
  <div class="app-container">

    <div class="filter-container">
      <el-input v-model="listQuery.title" placeholder="标题" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        搜索
      </el-button>
    </div>

    <el-button type="primary" style="margin-bottom: 20px" @click="handleAddAdministrators">添加食谱</el-button>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="rolesList"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange"
    >
      <el-table-column align="center" prop="id" sortable="custom" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="加入时间" width="150">
        <template slot-scope="scope">
          {{ scope.row.created_at }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="标题" width="120">
        <template slot-scope="scope">
          {{ scope.row.name }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="封面图" width="140">
        <template slot-scope="scope">
          <span class="link-type" @click="handleUpImage(scope.row.logo)"><img :src="scope.row.logo" width="50" height="50"></span>
        </template>
      </el-table-column>
      <el-table-column align="header-center" label="描述" min-width="180">
        <template slot-scope="scope">
          {{ scope.row.desc }}
        </template>
      </el-table-column>
      <el-table-column class-name="status-col" label="状态" width="80">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter2 }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" width="240">
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click="handleEdit(scope)">修改</el-button>
          <el-button v-if="scope.row.status!=1" size="mini" type="success" @click="handleModifyStatus(scope.row,1)">
            发布
          </el-button>
          <el-button v-if="scope.row.status!=0" size="mini" @click="handleModifyStatus(scope.row,0)">
            草稿
          </el-button>
          <el-button v-if="scope.row.status!=2" size="mini" type="danger" @click="handleModifyStatus(scope.row,2)">
            删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="fetchList" />

    <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'修改':'添加'">
      <el-form :model="role" label-width="80px" label-position="left">
        <el-form-item label="标题">
          <el-input v-model="role.name" placeholder="标题" />
        </el-form-item>
        <el-form-item label="描述">
          <el-input v-model="role.desc" placeholder="描述" />
        </el-form-item>
        <el-form-item label="播放地址">
          <el-input v-model="role.link" placeholder="播放地址" />
        </el-form-item>
        <el-form-item label="推荐指数">
          <el-rate
            v-model="role.star"
            :max="5"
            :colors="['#99A9BF', '#F7BA2A', '#FF9900']"
            :low-threshold="1"
            :high-threshold="5"
            style="display:inline-block"
          />
        </el-form-item>
        <el-form-item label="观看人数">
          <el-input v-model="role.number" placeholder="观看人数" />
        </el-form-item>
        <el-form-item label="公司logo" prop="title">
          <el-upload
            class="upload-demo"
            accept="image/png, image/jpeg, image/gif"
            :on-success="handleChangeOk"
            :action="uploadUrl"
          >
            <el-button size="small" type="primary">点击上传</el-button>
            <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过4M</div>
          </el-upload>
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="dialogVisible=false">放弃</el-button>
        <el-button type="primary" @click="confirmRole">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, fetchStatus, createArticle, updateArticle } from '@/api/videos'
import uploadLogoUrl from '@/utils/domain.js'
import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
import waves from '@/directive/waves' // waves directive
const defaultAdministrators = {
  id: '',
  name: '',
  desc: '',
  logo: '',
  link: '',
  star: 0,
  number: 0
}
export default {
  components: { uploadLogoUrl, Pagination },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        1: 'success',
        0: 'info',
        2: 'danger'
      }
      return statusMap[status]
    },
    statusFilter2(status) {
      const statusMap = {
        1: '发布',
        0: '草稿',
        2: '删除'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      role: Object.assign({}, defaultAdministrators),
      role_id: [],
      rolesList: [],
      dialogVisible: false,
      dialogType: 'new',
      checkStrictly: false,
      deleteRolePost: {
        id: ''
      },
      options: [],
      uploadUrl: uploadLogoUrl.uploadLogoUrl,
      listQuery: {
        page: 1,
        limit: 20,
        sort: 'desc',
        title: ''
      },
      sortOptions: [{ label: 'ID 升序', key: 'asc' }, { label: 'ID 降序', key: 'desc' }],
      postStatus: {
        id: null,
        status: null
      }
    }
  },
  computed: {
  },
  created() {
    // Mock: get all routes and roles list from server
    this.fetchList()
  },
  methods: {
    async fetchList() {
      this.listLoading = true
      const res = await fetchList(this.listQuery)
      this.rolesList = res.data.items
      this.total = res.data.total
      this.listLoading = false
      this.options = res.data.items
    },

    handleAddAdministrators() {
      this.role = Object.assign({}, defaultAdministrators)
      this.dialogType = 'new'
      this.dialogVisible = true
    },
    handleEdit(scope) {
      this.dialogType = 'edit'
      this.dialogVisible = true
      this.checkStrictly = true
      this.role = Object.assign({}, scope.row) // copy obj
      this.$nextTick(() => {
        this.checkStrictly = false
      })
    },
    handleDelete({ $index, row }) {
      this.$confirm('确认删除该管理员?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '放弃',
        type: 'warning'
      })
        .then(async() => {
          this.deleteRolePost.id = row.id
          await deleteAdministrator(this.deleteRolePost)
          this.rolesList.splice($index, 1)
          this.$message({
            type: 'success',
            message: 'Delete succed!'
          })
        })
        .catch(err => { })
    },
    async confirmRole() {
      const isEdit = this.dialogType === 'edit'

      if (isEdit) {
        await updateArticle(this.role)
      } else {
        const { data } = await createArticle(this.role)
        this.role.key = data.key
        this.rolesList.push(this.role)
      }

      this.dialogVisible = false
      this.$notify({
        title: 'Success',
        dangerouslyUseHTMLString: true,
        message: '成功',
        type: 'success'
      })
    },
    handleUpImage(img) {
      window.open(img, '_blank')
    },
    handleChangeOk(response) {
      this.role.logo = response.logo
    },
    sortChange(data) {
      const { prop, order } = data
      if (prop === 'id') {
        this.sortByID(order)
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = 'asc'
      } else {
        this.listQuery.sort = 'desc'
      }
      this.handleFilter()
    },
    handleFilter() {
      this.listQuery.page = 1
      this.fetchList()
    },
    handleModifyStatus(row, status) {
      this.postStatus.id = row.id
      this.postStatus.status = status
      fetchStatus(this.postStatus).then(response => {

      })
      this.$message({
        message: '操作成功',
        type: 'success'
      })
      row.status = status
    }
  }
}
</script>

<style lang="scss" scoped>
.app-container {
  .roles-table {
    margin-top: 30px;
  }
  .permission-tree {
    margin-bottom: 30px;
  }
}
</style>
