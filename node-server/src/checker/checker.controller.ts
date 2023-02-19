import { Controller, Body, Get, Param, Post } from '@nestjs/common';
import { CheckerService } from './checker.service';
import { SetTokenDto } from './types/setToken.dto';

@Controller('checker')
export class CheckerController {
  constructor(private readonly checkerService: CheckerService) {}

  @Get('check-login')
  async checkLogin() {
    const result = await this.checkerService.checkLogin();
    return result
  }

  // @Post('set-token/:token')
  // startCheck(@Param('token') token: string) {
  //   this.checkerService.startCheck(token);

  //   return 'ok';
  // }


  @Post('set-token')
  async setValue(@Body() setTokenDto: SetTokenDto) {
    return this.checkerService.setToken(
      setTokenDto.token
    );

  }
}
