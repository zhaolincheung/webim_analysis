
namespace java com.ganji.data.hive.thrift

/**
 * 向HADOOP提交HIVE任务类。典型的用法是提交任务，轮询任务是否完成，取得任务的结果URI，读取结果文件。
 *           long taskId = client.submitTask("lihongzhong@ganji.com", "web", "select * from web_pv_log_detail3 where dt = '2013-04-10' limit 10;");
 *           if (taskId <=0) {
 *               System.out.println("error submit");
 *               return;
 *           }
 *           int count = 50;
 *           while(count >= 0) {
 *               try {
 *                   Thread.sleep(30 * 1000);
 *               } catch (InterruptedException ex) {}
 *               if (client.isTaskFinished(taskId)) {
 *                   System.out.println(client.getResultURI(taskId));
 *                   break;
 *               } 
 *               count --;
 *          }
 */
service Hive {
    /** 提交任务
     * user - 用户名，ganji邮箱，如abc@ganji.com
     * env - 提交的环境。目前支持两个环境： mobile - 移动端， web - 主站。
     * sql - 提交的sql语句。
     * 返回值:成功提交返回大于0的任务id值，此id用于后续查询。失败返回0或-1.
     */
    i64 submitTask(1:string user, 2:string env, 3:string sql);
    /** 查看任务是否完成
     *  taskId - 任务号
     * 返回值：完成返回true，未完成返回false
     */
    bool isTaskFinished(1:i64 taskId);
    /** 取得任务结果的URI，可以用此URI获得结果数据
     *  taskId - 任务号
     * 返回值：任务有结果，返回URI，否则返回空字符串
     */
    string getResultURI(1:i64 taskId);
    /** 取得用户的所有任务列表
     *  user - 用户名，完整的ganji邮箱
     * 返回值：任务号列表，如果没有任务，返回空
     */
    list<i64> getTasksByUserName(1:string user);
}
