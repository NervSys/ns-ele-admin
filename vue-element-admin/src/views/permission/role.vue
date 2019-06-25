<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAddRole">添加角色</el-button>

    <el-table :data="rolesList" style="width: 100%;margin-top:30px;" border>
      <el-table-column align="center" label="角色标志" width="220">
        <template slot-scope="scope">
          {{ scope.row.sulg }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="角色名" width="220">
        <template slot-scope="scope">
          {{ scope.row.name }}
        </template>
      </el-table-column>
      <el-table-column align="header-center" label="角色描述">
        <template slot-scope="scope">
          {{ scope.row.info }}
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
        <el-form-item label="角色名">
          <el-input v-model="role.name" placeholder="角色名" />
        </el-form-item>
        <el-form-item label="角色标志">
          <el-input v-model="role.sulg" placeholder="角色标志" :readonly="dialogType==='edit'?true:false" />
        </el-form-item>
        <el-form-item label="描述">
          <el-input
            v-model="role.info"
            :autosize="{ minRows: 2, maxRows: 4}"
            type="textarea"
            placeholder="角色描述"
          />
        </el-form-item>
        <el-form-item label="权限列表">
          <el-drag-select v-model="role.routes" multiple placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.name"
              :value="item.value"
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
import { getRoutes, getRoles, addRole, deleteRole, updateRole } from '@/api/role'
import ElDragSelect from '@/components/DragSelect' // base on element-ui
const defaultRole = {
  id: '',
  sulg: '',
  name: '',
  info: '',
  routes: []
}
export default {
  components: { ElDragSelect },
  data() {
    return {
      role: Object.assign({}, defaultRole),
      routes: [],
      rolesList: [],
      dialogVisible: false,
      dialogType: 'new',
      checkStrictly: false,
      deleteRolePost: {
        id: ''
      },
      options: []
    }
  },
  computed: {
  },
  created() {
    // Mock: get all routes and roles list from server
    this.getRoutes()
    this.getRoles()
  },
  methods: {
    async getRoutes() {
      const res = await getRoutes()
      this.options = res.data
    },
    async getRoles() {
      const res = await getRoles()
      this.rolesList = res.data
    },
    handleAddRole() {
      this.role = Object.assign({}, defaultRole)
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
      this.$confirm('确认删除该角色?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '放弃',
        type: 'warning'
      })
        .then(async() => {
          this.deleteRolePost.id = row.id
          await deleteRole(this.deleteRolePost)
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
        await updateRole(this.role)
      } else {
        const { data } = await addRole(this.role)
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
