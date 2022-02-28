const todos = Vue.createApp({
	data(){
		return {
			todoList: [],
			todoItem: '',
			error: ''
		}
	},
	compilerOptions: {
		// change Vue JS delimiters - crash with symfony
		delimiters: ["${", "}"]
	},
	mounted(){
		this.getData();
	},
	methods: {
		deleteTodoItem(e) {
			const btn = e.target;
			const todoID = btn.getAttribute('data-id');
			// delet do item
			if(confirm("Do you really want to delete?")){
				fetch(`/todo/delete/${todoID} `);
				this.getData();
			}
		},

		getData() {
			// get todo list
			return 	fetch('/todolist')
					.then(res => res.json())
					.then(data => this.todoList = data)
					.catch(err => this.error = err.message)
		},

		addTodoItem() {
			// request option
			const requestOptions = {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
				},
				body: `description=${this.todoItem}`
			};

			// post to add new todo item
			fetch( '/todo/add', requestOptions )
				.then( res => res.json() )
				.then( data => {
					// update todo list
					this.getData();
					// clear description text field
					this.todoItem = '';
				})
				.catch((err) => {
					// update error message
					this.error = err.message;
					console.error(err);
				});
		}
	}
})

todos.mount("#todos");


const todo = Vue.createApp({
	data(){
		return {
			todoItem: null,
			todoID: '',
			error: '',
		}
	},
	compilerOptions: {
		// change Vue JS delimiters - crash with symfony
		delimiters: ["${", "}"]
	},
	
	mounted(){
		const pathname = window.location.pathname;
		const todoID = pathname.substring(pathname.lastIndexOf('/') + 1);
		console.log(todoID);
		this.getData(todoID);
	},
	methods: {
		deleteTodoItem(e) {
			const btn = e.target;
			const todoID = btn.getAttribute('data-id');
			// delet do item
			if(confirm("Do you really want to delete?")){
				fetch(`/todo/delete/${todoID} `);
				this.getData();
			}
		},

		getData(todoID) {
			// get todo list
			return 	fetch(`/todoitem/${todoID}`)
					.then(res =>  res.json())
					.then(data => this.todoItem =  data )
					.catch(err => this.error = err.message)
		},
	}
})

todo.mount("#todo");