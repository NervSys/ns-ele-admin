<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAddAdministrators">添加管理员</el-button>

    <el-table :data="rolesList" style="width: 100%;margin-top:30px;" border>
      <el-table-column align="center" label="加入时间" width="150">
        <template slot-scope="scope">
          {{ scope.row.created_at }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="登录名" width="120">
        <template slot-scope="scope">
          {{ scope.row.name }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="管理员图片" width="140">
        <template slot-scope="scope">
          <span class="link-type" @click="handleUpImage(scope.row.logo)"><img :src="scope.row.logo" width="50" height="50"></span>
        </template>
      </el-table-column>
      <el-table-column align="header-center" label="电话" width="100">
        <template slot-scope="scope">
          {{ scope.row.phone }}
        </template>
      </el-table-column>
      <el-table-column align="header-center" label="省份" width="100">
        <template slot-scope="scope">
          {{ scope.row.province }}
        </template>
      </el-table-column>
      <el-table-column align="header-center" label="城市">
        <template slot-scope="scope">
          {{ scope.row.city }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作">
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click="handleEdit(scope)">修改</el-button>
          <el-button type="danger" size="small" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'修改':'添加'">
      <el-form :model="role" label-width="80px" label-position="left">
        <el-form-item label="登录名">
          <el-input v-model="role.name" placeholder="管理员名字" :readonly="dialogType==='edit'?true:false" />
        </el-form-item>
        <el-form-item label="密码">
          <el-input v-model="role.pwd" placeholder="密码" />
        </el-form-item>
        <el-form-item label="电话">
          <el-input v-model="role.phone" placeholder="电话" />
        </el-form-item>
        <el-form-item label="省份">
          <el-input v-model="role.province" placeholder="省份" />
        </el-form-item>
        <el-form-item label="城市">
          <el-input v-model="role.city" placeholder="城市" />
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="role.email" placeholder="邮箱" />
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
        <el-form-item label="权限列表">
          <el-drag-select v-model="role.role_id" multiple placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-drag-select>
          <!--<div style="margin-top:30px;">-->
          <!--<el-tag v-for="item of role.routes" :key="item" style="margin-right:15px;">{{ item}}</el-tag>-->
          <!--</div>-->
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
import { getRoles, getAdministrators, addAdministrator, deleteAdministrator, updateAdministrator } from '@/api/administrator'
import uploadLogoUrl from '@/utils/domain.js'
import ElDragSelect from '@/components/DragSelect' // base on element-ui
const defaultAdministrators = {
  id: '',
  city: '',
  company: '',
  name: '',
  pwd: '',
  email: '',
  logo: '',
  phone: '',
  province: '',
  status: '',
  role_id: []
}
export default {
  components: { ElDragSelect, uploadLogoUrl },
  data() {
    return {
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
      uploadUrl: uploadLogoUrl.uploadLogoUrl
    }
  },
  computed: {
  },
  created() {
    // Mock: get all routes and roles list from server
    this.getRoles()
    this.getAdministrators()
  },
  methods: {
    async getRoles() {
      const res = await getRoles()
      this.options = res.data
    },
    async getAdministrators() {
      const res = await getAdministrators()
      this.rolesList = res.data
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
        await updateAdministrator(this.role)
      } else {
        const { data } = await addAdministrator(this.role)
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
